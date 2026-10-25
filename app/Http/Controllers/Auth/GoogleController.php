<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Support\AdminAlert;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $google = Socialite::driver('google')->user();

        // Returning Google user
        $user = User::where('google_id', $google->getId())->first();

        if (! $user) {
            $user = User::where('email', $google->getEmail())->first();

            // Email belongs to a password sign-up: do not log in
            if ($user && $user->has_password) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'The email has already been taken.']);
            }

            // New Google user, or an older Google-created account without google_id
            $user ??= new User([
                'name'         => $google->getName(),
                'email'        => $google->getEmail(),
                'password'     => Hash::make(Str::random(32)),
                'has_password' => false,
            ]);

            $user->google_id = $google->getId();
            $user->save();

            if ($user->wasRecentlyCreated) {
                AdminAlert::send('New user', $user->name . ' signed up with Google', 'heroicon-o-users', 'info');
            }
        }

        Auth::login($user, true);

        return redirect($user->is_admin ? '/admin' : '/');
    }
}