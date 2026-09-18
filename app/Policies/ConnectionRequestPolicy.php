<?php

namespace App\Policies;

use App\Enums\ConnectionStatus;
use App\Models\ConnectionRequest;
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
    public function view(User $user, ConnectionRequest $connectionRequest): bool
    {
        return (int) $connectionRequest->initiator_id === (int) $user->id
            || (int) $connectionRequest->recipient_id === (int) $user->id;
    }

    /**
     * Only the recipient can accept a request.
     */
    public function accept(User $user, ConnectionRequest $connectionRequest): bool
    {
        $statusValue = $connectionRequest->status instanceof ConnectionStatus
            ? $connectionRequest->status->value
            : (string) $connectionRequest->status;

        return (int) $connectionRequest->recipient_id === (int) $user->id
            && $statusValue === ConnectionStatus::Pending->value;
    }

    /**
     * Only the recipient can decline a request.
     */
    public function decline(User $user, ConnectionRequest $connectionRequest): bool
    {
        $statusValue = $connectionRequest->status instanceof ConnectionStatus
            ? $connectionRequest->status->value
            : (string) $connectionRequest->status;

        return (int) $connectionRequest->recipient_id === (int) $user->id
            && $statusValue === ConnectionStatus::Pending->value;
    }

    /**
     * Only the initiator can cancel a pending request.
     */
    public function cancel(User $user, ConnectionRequest $connectionRequest): bool
    {
        $statusValue = $connectionRequest->status instanceof ConnectionStatus
            ? $connectionRequest->status->value
            : (string) $connectionRequest->status;

        return (int) $connectionRequest->initiator_id === (int) $user->id
            && $statusValue === ConnectionStatus::Pending->value;
    }

    /**
     * Only the initiator can pay for an accepted request.
     */
    public function pay(User $user, ConnectionRequest $connectionRequest): bool
    {
        $statusValue = $connectionRequest->status instanceof ConnectionStatus
            ? $connectionRequest->status->value
            : (string) $connectionRequest->status;

        return (int) $connectionRequest->initiator_id === (int) $user->id
            && ($statusValue === ConnectionStatus::Accepted->value || $statusValue === ConnectionStatus::PaymentPending->value);
    }
}

