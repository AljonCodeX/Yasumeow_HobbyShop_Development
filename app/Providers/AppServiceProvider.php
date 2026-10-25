<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        RateLimiter::for('register', fn (Request $r) => Limit::perMinute(10)->by($r->ip()));
        RateLimiter::for('bag', fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('checkout', fn (Request $r) => Limit::perMinute(5)->by($r->user()?->id ?: $r->ip()));
        
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