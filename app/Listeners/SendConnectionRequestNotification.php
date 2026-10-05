<?php

namespace App\Listeners;

use App\Events\ConnectionRequestCreated;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionRequestNotification;
use Illuminate\Support\Facades\Log;

/**
 * Send a notification to the recipient when a new connection request arrives.
 */
class SendConnectionRequestNotification
{
    public function handle(ConnectionRequestCreated $event): void
    {
        try {
            $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
            if ($connectionRequest && $connectionRequest->recipient) {
                $connectionRequest->recipient->notify(new ConnectionRequestNotification($connectionRequest));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send ConnectionRequestNotification: ' . $e->getMessage(), [
                'connection_request_id' => $event->connectionRequestId,
            ]);
        }
    }
}

