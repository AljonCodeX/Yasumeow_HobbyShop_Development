<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Auth;

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
