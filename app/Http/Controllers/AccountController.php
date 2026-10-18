<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    // Dashboard = summary cards
    public function dashboard(Request $request)
    {
        $counts = Reservation::where('user_id', $request->user()->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            ['label' => 'Total Reservations', 'icon' => 'bi-receipt',          'count' => $counts->sum(),                                           'status' => null],
            ['label' => 'Awaiting Payment',   'icon' => 'bi-hourglass-split',  'count' => $counts['awaiting_payment'] ?? 0,                         'status' => 'awaiting_payment'],
            ['label' => 'Reserved',           'icon' => 'bi-bookmark-check',   'count' => $counts['pending'] ?? 0,                                  'status' => 'pending'],
            ['label' => 'Ready for Pickup',   'icon' => 'bi-box-seam',         'count' => $counts['ready'] ?? 0,                                    'status' => 'ready'],
            ['label' => 'Picked Up',          'icon' => 'bi-check-circle-fill', 'count' => $counts['picked_up'] ?? 0,                               'status' => 'picked_up'],
            ['label' => 'Cancelled',          'icon' => 'bi-x-circle-fill',    'count' => ($counts['cancelled'] ?? 0) + ($counts['expired'] ?? 0), 'status' => 'cancelled'],
        ];

        return view('account.dashboard', compact('stats'));
    }

    // Reservation history, with date range, status and search filters
    public function reservations(Request $request)
    {
        $data = $request->validate([
            'status' => ['nullable', 'string', 'max:30'],
            'from'   => ['nullable', 'date'],
            'to'     => ['nullable', 'date'],
            'q'      => ['nullable', 'string', 'max:60'],
        ]);

        $reservations = Reservation::with('items.product')
            ->where('user_id', $request->user()->id)
            ->when($data['status'] ?? null, fn ($q, $s) => $q->whereIn(
                'status',
                $s === 'cancelled' ? ['cancelled', 'expired'] : [$s]
            ))
            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('reserved_at', '>=', $d))
            ->when($data['to'] ?? null, fn ($q, $d) => $q->whereDate('reserved_at', '<=', $d))
            ->when($data['q'] ?? null, function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->whereHas('items.product', fn ($p) => $p->where('name', 'like', '%' . $term . '%'));

                    // Typing a number (or #12) also finds that reservation
                    $id = ltrim($term, '#');
                    if (ctype_digit($id)) {
                        $w->orWhere('id', $id);
                    }
                });
            })
            ->latest('reserved_at')
            ->paginate(8)
            ->withQueryString();

        return view('account.reservations', compact('reservations'));
    }

    public function info()
    {
        return view('account.info');
    }

    public function updateInfo(Request $request)
    {
        $request->merge(['phone' => preg_replace('/[\s-]/', '', (string) $request->input('phone'))]);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^(09|\+639)\d{9}$/'],
        ], [
            'phone.regex' => 'Enter a valid mobile number, like 09171234567.',
        ]);

        $user = $request->user();
        $user->name  = $data['name'];
        $user->phone = $data['phone'] ?: null;
        $user->save();

        return back()->with('success', 'Your account was updated.');
    }

    // Camera icon on the avatar: picking a file uploads it right away
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->profile_photo = $request->file('photo')->store('profiles', 'public');
        $user->save();

        return back()->with('success', 'Your profile photo was updated.');
    }

    public function removePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->profile_photo = null;
            $user->save();
        }

        return back()->with('success', 'Your profile photo was removed.');
    }

    public function updatePassword(Request $request)
    {
        // Own error bag, so these errors don't show up in the info form
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // The User model hashes the password automatically
        $request->user()->update(['password' => $request->password]);

        return back()->with('success', 'Your password was changed.');
    }

    public function favourites(Request $request)
    {
        $products = $request->user()->favourites()->with('category')->latest('favourites.created_at')->get();

        return view('account.favourites', compact('products'));
    }

    public function toggleFavourite(Request $request, Product $product)
    {
        $request->user()->favourites()->toggle($product->id);

        return back();
    }
}