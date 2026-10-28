<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Support\AdminAlert;

class ReservationController extends Controller
{
    public function add(Request $request, Product $product)
    {
        $cart = session('reserve', []);
        $qty = $cart[$product->id] ?? 0;

        // The Reserve button sends checkout=1: check out only this product, leave the bag alone
        $goToCheckout = $request->boolean('checkout');

        if ($product->stock_qty <= 0) {
            return back()->with('error', $product->name . ' is sold out.');
        }

        if ($goToCheckout) {
            session(['reserve_now' => [$product->id => 1]]);

            return redirect()->route('reserve.checkout');
        }

        if ($qty >= $product->stock_qty) {
            return back()->with('error', 'You already added all available stock of ' . $product->name . '.');
        }

        $cart[$product->id] = $qty + 1;
        session(['reserve' => $cart]);

        return back()->with('success', $product->name . ' added to your bag.');
    }

    public function increase(Product $product)
    {
        $cart = session('reserve', []);
        $qty = $cart[$product->id] ?? 0;

        if ($qty >= $product->stock_qty) {
            return back()->with('error', 'No more stock available for ' . $product->name . '.');
        }

        $cart[$product->id] = $qty + 1;
        session(['reserve' => $cart]);

        return back();
    }

    public function decrease(Product $product)
    {
        $cart = session('reserve', []);
        $qty = $cart[$product->id] ?? 0;

        if ($qty > 1) {
            $cart[$product->id] = $qty - 1;
        } else {
            unset($cart[$product->id]);
        }

        session(['reserve' => $cart]);

        return back();
    }

    public function remove(Product $product)
    {
        $cart = session('reserve', []);
        unset($cart[$product->id]);
        session(['reserve' => $cart]);

        return back();
    }

    // My Bag page
    public function bag()
    {
        // Coming to the bag means checking out the whole bag, not a single Reserve item
        session()->forget('reserve_now');

        $cart = $this->pruneCart('reserve');

        $products = Product::with('category')->whereIn('id', array_keys($cart))->get();

        $items = $products->map(fn ($p) => [
            'product'  => $p,
            'qty'      => $cart[$p->id],
            'subtotal' => (float) $p->price * $cart[$p->id],
        ]);

        $total = $items->sum('subtotal');

        return view('bag', compact('items', 'total'));
    }

    // Payment options page
    public function checkout()
    {
        $cart = $this->checkoutCart();

        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your bag is empty.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();

        $items = $products->map(fn ($p) => [
            'product'  => $p,
            'qty'      => $cart[$p->id],
            'subtotal' => (float) $p->price * $cart[$p->id],
        ]);

        $total = $items->sum('subtotal');
        $pickupBy = now()->addDays(Reservation::PICKUP_DAYS);

        return view('reserve-checkout', compact('items', 'total', 'pickupBy'));
    }

    public function confirm(Request $request)
    {
        $request->merge(['contact_phone' => preg_replace('/[\s-]/', '', (string) $request->input('contact_phone'))]);

        $data = $request->validate([
            'payment_option' => ['required', 'in:store,online'],
            'contact_name'   => ['required', 'string', 'max:255'],
            'contact_phone'  => ['required', 'regex:/^(09|\+639)\d{9}$/'],
            'note'           => ['nullable', 'string', 'max:255'],
        ], [
            'contact_phone.regex' => 'Enter a valid mobile number, like 09171234567.',
        ]);

        $option = $data['payment_option'];
        $cart = $this->checkoutCart();

        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your bag is empty.');
        }

        // Lock the product rows so two people can't reserve the last item at the same time
        $result = DB::transaction(function () use ($cart, $option, $data) {
            $products = Product::whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get();

            // A product in the bag was deleted by the admin
            if ($products->count() !== count($cart)) {
                return 'Some items in your bag are no longer available.';
            }

            // Check the stock again, it may have changed since the item was added
            foreach ($products as $product) {
                if ($product->stock_qty < $cart[$product->id]) {
                    return 'Not enough stock for ' . $product->name . '. Only ' . $product->stock_qty . ' left.';
                }
            }

            $total = $products->sum(fn ($p) => (float) $p->price * $cart[$p->id]);
            $due = $option === 'online' ? $total : 0;

            $reservation = Reservation::create([
                'user_id'        => Auth::id(),
                'contact_name'   => $data['contact_name'],
                'contact_phone'  => $data['contact_phone'],
                'note'           => $data['note'] ?? null,
                'reserved_at'    => now(),
                'pickup_by'      => $option === 'store' ? now()->addDays(Reservation::PICKUP_DAYS) : null,
                'payment_method' => $option,
                'status'         => $option === 'store' ? 'pending' : 'awaiting_payment',
                'amount_total'   => $total,
                'amount_due'     => $due,
                'amount_paid'    => 0,
            ]);

            foreach ($products as $product) {
                $qty = $cart[$product->id];

                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'product_id'     => $product->id,
                    'quantity'       => $qty,
                    'unit_price'     => $product->price,
                ]);

                $product->decrement('stock_qty', $qty);
            }

            return $reservation;
        });

        // The transaction returned an error message instead of a reservation
        if (is_string($result)) {
            return redirect()->route('reserve.checkout')->with('error', $result);
        }

        $reservation = $result;

        // Pay in store is confirmed right away. Online reservations alert when the payment arrives.
        if ($option === 'store') {
            AdminAlert::send(
                'New reservation ' . $reservation->reference,
                $reservation->contact_name . ' reserved ₱' . number_format((float) $reservation->amount_total, 2) . ' (pay in store)',
                'heroicon-o-clipboard-document-check',
                'info'
            );
        }

        // Low stock warning for the items just reserved
        Product::whereIn('id', array_keys($cart))->where('stock_qty', '<=', 5)->get()
            ->each(fn ($p) => AdminAlert::send(
                $p->stock_qty <= 0 ? 'Sold out' : 'Low stock',
                $p->name . ($p->stock_qty <= 0 ? ' is sold out.' : ' has only ' . $p->stock_qty . ' left.'),
                'heroicon-o-exclamation-triangle',
                $p->stock_qty <= 0 ? 'danger' : 'warning'
            ));

        // Pay in store: nothing to pay now
        if ($option === 'store') {
            $this->clearCheckoutCart();

            return redirect()->route('reservations.thanks', $reservation);
        }

        // Online: create the PayMongo checkout and send the customer there
        $checkoutUrl = $this->createPayMongoCheckout($reservation);

        if (! $checkoutUrl) {
            $reservation->cancelAndRestock();

            return redirect()->route('reserve.checkout')
                ->with('error', 'We could not start the payment. Please try again.');
        }

        $this->clearCheckoutCart();

        return redirect()->away($checkoutUrl);
    }

    // Shown after paying (or after choosing pay in store)
    public function thanks(Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $reservation->load('items.product.category');

        return view('reservation-thanks', compact('reservation'));
    }

    // PayMongo sends the customer here when they back out of the payment page
    public function cancelled(Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        return redirect()->route('shop')
            ->with('error', 'Payment not completed. Your items are held for 30 minutes, then released automatically.');
    }

    // Drops products that no longer exist from a session cart
    private function pruneCart(string $key): array
    {
        $cart = session($key, []);

        if (empty($cart)) {
            return [];
        }

        $existing = Product::whereIn('id', array_keys($cart))->pluck('id')->all();
        $clean = array_intersect_key($cart, array_flip($existing));

        if (count($clean) !== count($cart)) {
            session([$key => $clean]);
        }

        return $clean;
    }

    // What checkout uses: the single Reserve item if there is one, otherwise the whole bag
    private function checkoutCart(): array
    {
        return session()->has('reserve_now')
        ? $this->pruneCart('reserve_now')
        : $this->pruneCart('reserve');
    }

    // After a successful checkout, clear only what was checked out
    private function clearCheckoutCart(): void
    {
        session()->forget(session()->has('reserve_now') ? 'reserve_now' : 'reserve');
    }

    // Returns the PayMongo checkout URL, or null if something went wrong
    private function createPayMongoCheckout(Reservation $reservation): ?string
    {
        $lineItems = $reservation->items->map(fn ($item) => [
            'name'     => Product::find($item->product_id)->name,
            'amount'   => (int) round((float) $item->unit_price * 100), // PayMongo uses centavos
            'currency' => 'PHP',
            'quantity' => $item->quantity,
        ])->all();

        try {
            $response = Http::timeout(15)
                ->withBasicAuth(config('services.paymongo.secret_key'), '')
                ->post('https://api.paymongo.com/v2/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'line_items'           => $lineItems,
                            'payment_method_types' => ['qrph'],
                            'success_url'          => route('reservations.thanks', $reservation),
                            'cancel_url'           => route('reservations.cancelled', $reservation),
                            'reference_number'     => 'RES-' . $reservation->reference,
                            'description'          => 'Yasumeow Hobby Shop reservation ' . $reservation->reference,
                        ],
                    ],
                ]);
        } catch (\Throwable $e) {
            Log::error('PayMongo request failed', ['message' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::error('PayMongo checkout failed', ['body' => $response->body()]);

            return null;
        }

        $reservation->update(['paymongo_session_id' => $response->json('data.id')]);

        return $response->json('data.attributes.checkout_url');
    }
}