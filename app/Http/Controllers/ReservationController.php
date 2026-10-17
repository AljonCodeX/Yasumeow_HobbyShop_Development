<?php

namespace App\Http\Controllers;

use App\Models\Product;

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

        return back()->with('success', $product->name . ' added to your reservation.');
    }
}