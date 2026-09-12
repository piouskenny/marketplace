<?php

namespace App\Policies;

use App\Models\User;

/**
 * Authorization policy for connection requests.
 *
 * Controls who can view, accept, decline, cancel, or pay for
 * a connection request. Policies are enforced at the application
 * boundary (Livewire components, controllers) before delegating
 * to Actions.
 */
class ConnectionRequestPolicy
{
    /**
     * Only the initiator or recipient can view a connection request.
     */
    public function view(User $user, mixed $connectionRequest): bool
    {
        // Placeholder — will check $user->id matches initiator_id or recipient_id
        return false;
    }

    /**
     * Only the recipient can accept a request.
     */
    public function accept(User $user, mixed $connectionRequest): bool
    {
        // Placeholder — will check $user->id === $connectionRequest->recipient_id
        //            && $connectionRequest->status === ConnectionStatus::Pending
        return false;
    }

    /**
     * Only the recipient can decline a request.
     */
    public function decline(User $user, mixed $connectionRequest): bool
    {
        return false;
    }

    /**
     * Only the initiator can cancel a pending request.
     */
    public function cancel(User $user, mixed $connectionRequest): bool
    {
        return false;
    }

    /**
     * Only the initiator can pay for an accepted request.
     */
    public function pay(User $user, mixed $connectionRequest): bool
    {
        // Placeholder — will check $user->id === $connectionRequest->initiator_id
        //            && $connectionRequest->status === ConnectionStatus::PaymentPending
        return false;
    }
}
