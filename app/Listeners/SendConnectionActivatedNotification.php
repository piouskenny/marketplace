<?php

namespace App\Listeners;

use App\Events\ConnectionActivated;

/**
 * Send notifications to both parties when a connection is activated.
 */
class SendConnectionActivatedNotification
{
    public function handle(ConnectionActivated $event): void
    {
        // Placeholder — will load the ConnectionRequest, find both users,
        // and send "Connection Activated" notifications.
    }
}
