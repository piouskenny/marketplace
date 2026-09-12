<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a payment is verified and a connection is activated.
 *
 * Listeners: send "connection activated" notification to both parties,
 *            unlock contact details, enable chat.
 */
class ConnectionActivated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $connectionRequestId,
        public readonly int $paymentId,
    ) {}
}
