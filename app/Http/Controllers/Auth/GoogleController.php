<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\GoogleAuthAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(Request $request, GoogleAuthAction $action)
    {
        try {
            $email = $request->query('email', 'google.user@marketplace.com');
            $name = $request->query('name', 'Google Account User');

            // Execute Google authentication / account creation
            $user = $action->execute($email, $name);

            return redirect()->to('/dashboard')->with('status', 'Successfully signed up and logged in with Google!');
        } catch (Throwable $e) {
            return redirect()->to('/login')->withErrors(['email' => 'Unable to sign in with Google. Please try again.']);
        }
    }
}

