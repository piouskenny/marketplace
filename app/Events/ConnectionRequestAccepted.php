<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a connection request is accepted.
 *
 * Listeners: send "payment required" notification to initiator.
 */
class ConnectionRequestAccepted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $connectionRequestId,
    ) {}
}
