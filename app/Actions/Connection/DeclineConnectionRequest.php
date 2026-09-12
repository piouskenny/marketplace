<?php

namespace App\Actions\Connection;

use App\Enums\ConnectionStatus;

/**
 * Decline a pending connection request.
 *
 * Transitions status from Pending → Declined. No payment occurs.
 *
 * Used by: Livewire components, future API controllers.
 */
class DeclineConnectionRequest
{
    public function execute(int $connectionRequestId, int $recipientUserId): mixed
    {
        // Placeholder — full implementation after models/migrations are approved.
        //
        // Intended flow:
        //   1. Load ConnectionRequest, authorize recipient
        //   2. Validate current status is Pending
        //   3. Update status to Declined, set declined_at
        //   4. Dispatch ConnectionRequestDeclined event
        //   5. Return updated ConnectionRequest

        throw new \RuntimeException('DeclineConnectionRequest action is not yet implemented.');
    }
}
