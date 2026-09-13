<?php

namespace App\Actions\Profile;

use App\Models\User;

class UpdateCustomerProfileAction
{
    /**
     * Complete onboarding for customer / hirer intent.
     */
    public function execute(User $user, array $data): User
    {
        $user->update([
            'phone' => $data['phone'] ?? $user->phone,
            'location' => $data['location'] ?? $user->location ?? 'Nigeria',
            'onboarding_intent' => 'hire',
            'onboarding_completed' => true,
        ]);

        return $user;
    }
}
