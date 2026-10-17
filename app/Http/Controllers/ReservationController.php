<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function add(Product $product)
    {
        $cart = session('reserve', []);
        $qty = $cart[$product->id] ?? 0;

        if ($product->stock_qty <= 0) {
            return back()->with('error', $product->name . ' is sold out.');
        }

        if ($qty >= $product->stock_qty) {
            return back()->with('error', 'You already reserved all available stock of ' . $product->name . '.');
        }

        $cart[$product->id] = $qty + 1;
        session(['reserve' => $cart]);

        return back()
            ->with('success', $product->name . ' added to your reservation.')
            ->with('open_reserve', true);
    }

    public function increase(Product $product)
    {
        $cart = session('reserve', []);
        $qty = $cart[$product->id] ?? 0;

        if ($qty >= $product->stock_qty) {
            return back()
                ->with('error', 'No more stock available for ' . $product->name . '.')
                ->with('open_reserve', true);
        }

        $cart[$product->id] = $qty + 1;
        session(['reserve' => $cart]);

        return back()->with('open_reserve', true);
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

        return back()->with('open_reserve', true);
    }

    public function remove(Product $product)
    {
        $cart = session('reserve', []);
        unset($cart[$product->id]);
        session(['reserve' => $cart]);

        return back()->with('open_reserve', true);
    }

    public function confirm()
    {
        $cart = session('reserve', []);

        if (empty($cart)) {
            return back()
                ->with('error', 'Your reservation is empty.')
                ->with('open_reserve', true);
        }

        // Lock the product rows so two people can't reserve the last item at the same time
        [$type, $message] = DB::transaction(function () use ($cart) {
            $products = Product::whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get();

            // A product in the cart was deleted by the admin
            if ($products->count() !== count($cart)) {
                return ['error', 'Some items in your reservation are no longer available.'];
            }

            // Check the stock again, it may have changed since the item was added
            foreach ($products as $product) {
                if ($product->stock_qty < $cart[$product->id]) {
                    return ['error', 'Not enough stock for ' . $product->name . '. Only ' . $product->stock_qty . ' left.'];
                }
            }

            $reservation = Reservation::create([
                'user_id'        => Auth::id(),
                'reserved_at'    => now(),
                'payment_method' => 'store',
                'status'         => 'pending',
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

            return ['success', 'Reservation confirmed! Pick it up at the shop and pay in store or GCash.'];
        });

        if ($type === 'error') {
            return back()
                ->with('error', $message)
                ->with('open_reserve', true);
        }

        session()->forget('reserve');

        return back()->with('success', $message);
    }
}