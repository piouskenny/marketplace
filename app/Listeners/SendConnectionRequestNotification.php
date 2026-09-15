<?php

namespace App\Listeners;

use App\Events\ConnectionRequestCreated;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionRequestNotification;

/**
 * Send a notification to the recipient when a new connection request arrives.
 */
class SendConnectionRequestNotification
{
    public function handle(ConnectionRequestCreated $event): void
    {
        $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
        if ($connectionRequest && $connectionRequest->recipient) {
            $connectionRequest->recipient->notify(new ConnectionRequestNotification($connectionRequest));
        }
    }
}

