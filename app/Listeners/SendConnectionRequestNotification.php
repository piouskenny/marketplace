<?php

namespace App\Listeners;

use App\Events\ConnectionRequestCreated;

/**
 * Send a notification to the recipient when a new connection request arrives.
 *
 * This listener is a secondary side effect — the primary action
 * (creating the request) succeeds regardless of notification delivery.
 */
class SendConnectionRequestNotification
{
    public function handle(ConnectionRequestCreated $event): void
    {
        // Placeholder — will load the ConnectionRequest, find the recipient,
        // and send a Laravel Notification (database + email).
    }
}
