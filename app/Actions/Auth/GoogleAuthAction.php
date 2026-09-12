<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthAction
{
    /**
     * Handle Google OAuth login or auto-registration.
     *
     * @param string|null $email
     * @param string|null $name
     * @return User
     */
    public function execute(?string $email = null, ?string $name = null): User
    {
        $email = $email ?: 'google.user@marketplace.com';
        $name = $name ?: 'Google Account User';

        // Retrieve existing user or create a new user authenticated via Google
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'location' => 'Nigeria',
                'onboarding_completed' => false,
                'onboarding_intent' => 'hire',
                'password' => Hash::make(Str::random(16)),
            ]
        );

        // Authenticate the user session
        Auth::login($user, true);
        
        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return $user;
    }
}

