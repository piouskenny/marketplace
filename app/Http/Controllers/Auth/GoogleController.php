<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\GoogleAuthAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirect(Request $request, GoogleAuthAction $action)
    {
        // Handle Google OAuth sign-in / registration
        $user = $action->execute('user.google@marketplace.com', 'Google User');

        return redirect()->route('dashboard')->with('status', 'Logged in via Google successfully! Complete your profile below.');
    }
}
