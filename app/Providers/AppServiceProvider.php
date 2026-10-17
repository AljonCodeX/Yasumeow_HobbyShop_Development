<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Gives the layout the reservation list, so the navbar badge and drawer work on every page
        View::composer('layouts.app', function ($view) {
            $cart = session('reserve', []);
            $products = Product::whereIn('id', array_keys($cart))->get();

            $items = $products->map(fn ($p) => [
                'product'  => $p,
                'qty'      => $cart[$p->id],
                'subtotal' => (float) $p->price * $cart[$p->id],
            ]);

            $view->with([
                'reserveItems' => $items,
                'reserveCount' => $items->sum('qty'),
                'reserveTotal' => $items->sum('subtotal'),
            ]);
        });
    }
}