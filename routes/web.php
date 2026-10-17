<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::get('/', [PageController::class, 'home'])->name('welcome');

Route::get('/hello', function() {
    return 'Hello From Yasumeow Hobby Shop!';
});

Auth::routes();

// Login sends people to /home, so send them on to the real home page
Route::get('/home', fn () => redirect('/'))->name('home');

Route::get('/games', [PageController::class, 'games'])->name('games');
Route::get('/games/{slug}', [PageController::class, 'gameDetails'])->name('games.show');

Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/register', [EventController::class, 'register'])->middleware('auth')->name('events.register');
Route::delete('/events/{event}/register', [EventController::class, 'cancel'])->middleware('auth')->name('events.cancel');

Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/shop/{product}', [ProductController::class, 'show'])->name('products.show');

Route::post('/reserve/confirm', [ReservationController::class, 'confirm'])->middleware('auth')->name('reserve.confirm');
Route::post('/reserve/{product}', [ReservationController::class, 'add'])->middleware('auth')->name('reserve.add');
Route::patch('/reserve/{product}/increase', [ReservationController::class, 'increase'])->middleware('auth')->name('reserve.increase');
Route::patch('/reserve/{product}/decrease', [ReservationController::class, 'decrease'])->middleware('auth')->name('reserve.decrease');
Route::delete('/reserve/{product}', [ReservationController::class, 'remove'])->middleware('auth')->name('reserve.remove');