<?php

namespace App\Listeners;

use App\Events\ConnectionActivated;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionActivatedNotification;

/**
 * Send notifications to both parties when a connection is activated.
 */
class SendConnectionActivatedNotification
{
    public function handle(ConnectionActivated $event): void
    {
        $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
        if ($connectionRequest) {
            if ($connectionRequest->initiator) {
                $connectionRequest->initiator->notify(new ConnectionActivatedNotification($connectionRequest));
            }
            if ($connectionRequest->recipient) {
                $connectionRequest->recipient->notify(new ConnectionActivatedNotification($connectionRequest));
            }
        }
    }
}

