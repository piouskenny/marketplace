<?php

namespace App\Policies;

use App\Models\User;

/**
 * Authorization policy for opportunities.
 *
 * Controls who can view, create, edit, close, or delete opportunities.
 */
class OpportunityPolicy
{
    /**
     * Anyone can view an open opportunity.
     */
    public function view(User $user, mixed $opportunity): bool
    {
        return true;
    }

    /**
     * Any authenticated user can create an opportunity.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the owner can update their opportunity.
     */
    public function update(User $user, mixed $opportunity): bool
    {
        return false; // Placeholder — will check $user->id === $opportunity->user_id
    }

    /**
     * Only the owner can delete their opportunity.
     */
    public function delete(User $user, mixed $opportunity): bool
    {
        return false;
    }
}
