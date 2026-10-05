<?php

namespace App\Listeners;

use App\Events\ConnectionRequestAccepted;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionAcceptedNotification;
use Illuminate\Support\Facades\Log;

class SendConnectionAcceptedNotification
{
    public function handle(ConnectionRequestAccepted $event): void
    {
        try {
            $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
            if ($connectionRequest && $connectionRequest->initiator) {
                $connectionRequest->initiator->notify(new ConnectionAcceptedNotification($connectionRequest));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send ConnectionAcceptedNotification: ' . $e->getMessage(), [
                'connection_request_id' => $event->connectionRequestId,
            ]);
        }
    }
}
