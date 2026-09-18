<?php

namespace App\Policies;

use App\Models\ProfessionalProfile;
use App\Models\User;

class ProfilePolicy
{
    /**
     * Anyone can view public profiles.
     */
    public function view(?User $user, ProfessionalProfile $profile): bool
    {
        return true;
    }

    /**
     * Only the profile owner can edit or update their profile.
     */
    public function update(User $user, ProfessionalProfile $profile): bool
    {
        return (int) $user->id === (int) $profile->user_id;
    }

    /**
     * Determine whether the user can see unmasked private contact information for this profile.
     */
    public function viewContactDetails(User $user, ProfessionalProfile $profile): bool
    {
        return $profile->canSeeContactDetails($user);
    }
}
