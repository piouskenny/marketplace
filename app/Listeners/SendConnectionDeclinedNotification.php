<?php

namespace App\Listeners;

use App\Events\ConnectionRequestDeclined;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionDeclinedNotification;

class SendConnectionDeclinedNotification
{
    public function handle(ConnectionRequestDeclined $event): void
    {
        $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
        if ($connectionRequest && $connectionRequest->initiator) {
            $connectionRequest->initiator->notify(new ConnectionDeclinedNotification($connectionRequest));
        }
    }
}
