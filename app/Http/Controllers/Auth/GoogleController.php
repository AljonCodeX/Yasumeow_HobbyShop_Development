<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $google = Socialite::driver('google')->user();

        // Matches by email: an existing account with the same email is logged in
        $user = User::firstOrCreate(
            ['email' => $google->getEmail()],
            ['name' => $google->getName(), 'password' => Hash::make(Str::random(32))]
        );

        Auth::login($user, true);

        return redirect('/');
    }
}