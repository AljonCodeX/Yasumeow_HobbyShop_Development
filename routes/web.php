<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PayMongoWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Auth::routes();

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// Login sends people to /home, so send them on to the real home page
Route::get('/home', fn () => redirect('/'))->name('home');

/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('welcome');

Route::get('/games', [PageController::class, 'games'])->name('games');
Route::get('/games/{slug}', [PageController::class, 'gameDetails'])->name('games.show');

Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/shop/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'data'])
    ->middleware('throttle:60,1')
    ->name('search.suggestions');

/*
|--------------------------------------------------------------------------
| Logged-in users
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/settings', fn () => view('settings'))->name('settings');

    // Events
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/register', [EventController::class, 'cancel'])->name('events.cancel');

    // Reservation bag and checkout
    Route::get('/reserve/checkout', [ReservationController::class, 'checkout'])->name('reserve.checkout');
    Route::post('/reserve/confirm', [ReservationController::class, 'confirm'])->name('reserve.confirm');
    Route::post('/reserve/{product}', [ReservationController::class, 'add'])->name('reserve.add');
    Route::patch('/reserve/{product}/increase', [ReservationController::class, 'increase'])->name('reserve.increase');
    Route::patch('/reserve/{product}/decrease', [ReservationController::class, 'decrease'])->name('reserve.decrease');
    Route::delete('/reserve/{product}', [ReservationController::class, 'remove'])->name('reserve.remove');

    // After checkout
    Route::get('/reservations/{reservation}/thanks', [ReservationController::class, 'thanks'])->name('reservations.thanks');
    Route::get('/reservations/{reservation}/cancelled', [ReservationController::class, 'cancelled'])->name('reservations.cancelled');
});

/*
|--------------------------------------------------------------------------
| Webhooks (no login, checked by signature instead)
|--------------------------------------------------------------------------
*/
Route::post('/webhooks/paymongo', [PayMongoWebhookController::class, 'handle'])->name('paymongo.webhook');