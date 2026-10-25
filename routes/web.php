<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PayMongoWebhookController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RatingController;

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

Route::view('/about', 'about')->name('about');

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

     // My Account
    Route::redirect('/settings', '/account/appearance')->name('settings');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/reservations', [AccountController::class, 'reservations'])->name('reservations');
        Route::get('/bag', [ReservationController::class, 'bag'])->name('bag');
        Route::get('/info', [AccountController::class, 'info'])->name('info');
        Route::put('/info', [AccountController::class, 'updateInfo'])->name('info.update');
        Route::post('/photo', [AccountController::class, 'updatePhoto'])->name('photo.update');
        Route::delete('/photo', [AccountController::class, 'removePhoto'])->name('photo.remove');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
        Route::get('/favourites', [AccountController::class, 'favourites'])->name('favourites');
        Route::post('/favourites/{product}', [AccountController::class, 'toggleFavourite'])->name('favourites.toggle');
        Route::view('/appearance', 'account.appearance')->name('appearance');
    });
    // Events
    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/register', [EventController::class, 'cancel'])->name('events.cancel');

    // Reservation bag and checkout
    Route::get('/reserve/checkout', [ReservationController::class, 'checkout'])->name('reserve.checkout');
    Route::get('/bag', [ReservationController::class, 'bag'])->name('bag');
    Route::post('/reserve/confirm', [ReservationController::class, 'confirm'])
    ->middleware('throttle:checkout')
    ->name('reserve.confirm');

    Route::middleware('throttle:bag')->group(function () {
    Route::post('/reserve/{product}', [ReservationController::class, 'add'])->name('reserve.add');
    Route::patch('/reserve/{product}/increase', [ReservationController::class, 'increase'])->name('reserve.increase');
    Route::patch('/reserve/{product}/decrease', [ReservationController::class, 'decrease'])->name('reserve.decrease');
    Route::delete('/reserve/{product}', [ReservationController::class, 'remove'])->name('reserve.remove');
});

    // After checkout
    Route::get('/reservations/{reservation}/thanks', [ReservationController::class, 'thanks'])->name('reservations.thanks');
    Route::get('/reservations/{reservation}/cancelled', [ReservationController::class, 'cancelled'])->name('reservations.cancelled');
    Route::post('/reservations/{reservation}/rating', [RatingController::class, 'store'])->name('reservations.rate');
    Route::post('/events/{event}/rating', [RatingController::class, 'storeEvent'])->name('events.rate');
});

/*
|--------------------------------------------------------------------------
| Webhooks (no login, checked by signature instead)
|--------------------------------------------------------------------------
*/
Route::post('/webhooks/paymongo', [PayMongoWebhookController::class, 'handle'])->name('paymongo.webhook');