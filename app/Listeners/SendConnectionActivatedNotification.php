<?php

namespace App\Listeners;

use App\Events\ConnectionActivated;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionActivatedNotification;
use Illuminate\Support\Facades\Log;

/**
 * Send notifications to both parties when a connection is activated.
 */
class SendConnectionActivatedNotification
{
    public function handle(ConnectionActivated $event): void
    {
        try {
            $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
            if ($connectionRequest) {
                if ($connectionRequest->initiator) {
                    try {
                        $connectionRequest->initiator->notify(new ConnectionActivatedNotification($connectionRequest, $connectionRequest->initiator_id));
                    } catch (\Throwable $e) {
                        Log::error('Failed to send initiator ConnectionActivatedNotification: ' . $e->getMessage());
                    }
                }
                if ($connectionRequest->recipient) {
                    try {
                        $connectionRequest->recipient->notify(new ConnectionActivatedNotification($connectionRequest, $connectionRequest->recipient_id));
                    } catch (\Throwable $e) {
                        Log::error('Failed to send recipient ConnectionActivatedNotification: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send ConnectionActivatedNotification: ' . $e->getMessage(), [
                'connection_request_id' => $event->connectionRequestId,
            ]);
        }
    }
}

