<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
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
