<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a new connection request is created.
 *
 * Listeners: send notification to recipient.
 */
class ConnectionRequestCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $connectionRequestId,
    ) {}
}
