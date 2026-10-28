<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/home';

    // Google users get a real password now, so My Account shows "Change Password"
    protected function setUserPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->has_password = true;
    }

    // The layout shows the "success" message, not "status"
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('success', 'Your password was reset. You are now logged in.');
    }
}