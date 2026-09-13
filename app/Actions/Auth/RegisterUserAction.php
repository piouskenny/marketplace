<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterUserAction
{
    /**
     * Register a new user in the system.
     *
     * @param array $data
     * @return User
     */
    public function execute(array $data): User
    {
        $name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        if (empty($name)) {
            $name = $data['name'] ?? 'User';
        }

        $user = User::create([
            'name' => $name,
            'email' => $data['email'],
            'location' => $data['country'] ?? 'Nigeria',
            'onboarding_completed' => false,
            'onboarding_intent' => $data['type'] ?? 'hire',
            'password' => Hash::make($data['password']),
        ]);

        try {
            event(new \Illuminate\Auth\Events\Registered($user));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send verification email: ' . $e->getMessage());
        }

        return $user;
    }
}
