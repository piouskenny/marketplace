<?php

namespace App\Actions\Profile;

use App\Models\User;

class UpdateCustomerProfileAction
{
    /**
     * Complete onboarding for a customer / hirer.
     *
     * No talent classification is registered — this user is choosing to use
     * Skill Link NG purely to find and hire service providers.
     */
    public function execute(User $user, array $data): User
    {
        $user->update([
            'phone'                => $data['phone'] ?? $user->phone,
            'location'             => $data['location'] ?? $user->location ?? 'Nigeria',
            'onboarding_intent'    => 'hire',
            'onboarding_completed' => true,
        ]);

        return $user;
    }

    /**
     * Skip talent onboarding ("Skip for now").
     *
     * The user has chosen not to register as any talent classification for now.
     * onboarding_completed is set to true so they can access all platform features
     * (applying to opportunities, posting jobs, messaging).
     * No talent_type_user rows are created.
     */
    public function skip(User $user): User
    {
        $user->update([
            'onboarding_intent'    => 'skip',
            'onboarding_completed' => true,
        ]);

        return $user;
    }
}
