<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a connection request is declined.
 *
 * Listeners: send "request declined" notification to initiator.
 */
class ConnectionRequestDeclined
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $connectionRequestId,
    ) {}
}
