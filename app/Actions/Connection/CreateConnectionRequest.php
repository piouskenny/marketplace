<?php

namespace App\Actions\Connection;

use App\DataTransferObjects\CreateConnectionRequestData;
use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use Illuminate\Support\Facades\DB;

/**
 * Create a new connection request.
 *
 * This action validates business rules (no self-connection, no duplicate active
 * requests) and creates the connection_request record inside a transaction.
 *
 * Used by: Livewire components, future API controllers.
 */
class CreateConnectionRequest
{
    /**
     * @throws \App\Exceptions\DuplicateConnectionException
     * @throws \InvalidArgumentException
     */
    public function execute(CreateConnectionRequestData $data): mixed
    {
        // Placeholder — full implementation after models/migrations are approved.
        //
        // Intended flow:
        //   1. Validate initiator ≠ recipient
        //   2. Check no active duplicate request exists
        //   3. If opportunity-based, verify opportunity accepts applications
        //   4. DB::transaction — create ConnectionRequest with status Pending
        //   5. Dispatch ConnectionRequestCreated event
        //   6. Return the created ConnectionRequest model
        //
        // return DB::transaction(function () use ($data) { ... });

        throw new \RuntimeException('CreateConnectionRequest action is not yet implemented.');
    }
}
