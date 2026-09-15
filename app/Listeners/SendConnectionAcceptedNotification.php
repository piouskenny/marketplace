<?php

namespace App\Listeners;

use App\Events\ConnectionRequestAccepted;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionAcceptedNotification;

class SendConnectionAcceptedNotification
{
    public function handle(ConnectionRequestAccepted $event): void
    {
        $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
        if ($connectionRequest && $connectionRequest->initiator) {
            $connectionRequest->initiator->notify(new ConnectionAcceptedNotification($connectionRequest));
        }
    }
}
