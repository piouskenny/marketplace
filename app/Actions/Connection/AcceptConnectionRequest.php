<?php

namespace App\Actions\Connection;

use App\Enums\ConnectionStatus;
use Illuminate\Support\Facades\DB;

/**
 * Accept a pending connection request.
 *
 * Transitions status from Pending → Accepted → PaymentPending and
 * dispatches the ConnectionRequestAccepted event so listeners can
 * notify the initiator that payment is required.
 *
 * Used by: Livewire components, future API controllers.
 */
class AcceptConnectionRequest
{
    /**
     * @throws \App\Exceptions\InvalidConnectionTransitionException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function execute(int $connectionRequestId, int $recipientUserId): mixed
    {
        // Placeholder — full implementation after models/migrations are approved.
        //
        // Intended flow:
        //   1. Load ConnectionRequest, authorize recipient
        //   2. Validate current status allows acceptance
        //   3. DB::transaction — update status to PaymentPending, set accepted_at
        //   4. Dispatch ConnectionRequestAccepted event
        //   5. Return updated ConnectionRequest

        throw new \RuntimeException('AcceptConnectionRequest action is not yet implemented.');
    }
}
