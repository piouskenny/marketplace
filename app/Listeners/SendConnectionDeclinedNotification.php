<?php

namespace App\Listeners;

use App\Events\ConnectionRequestDeclined;
use App\Models\ConnectionRequest;
use App\Notifications\ConnectionDeclinedNotification;
use Illuminate\Support\Facades\Log;

class SendConnectionDeclinedNotification
{
    public function handle(ConnectionRequestDeclined $event): void
    {
        try {
            $connectionRequest = ConnectionRequest::find($event->connectionRequestId);
            if ($connectionRequest && $connectionRequest->initiator) {
                $connectionRequest->initiator->notify(new ConnectionDeclinedNotification($connectionRequest));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send ConnectionDeclinedNotification: ' . $e->getMessage(), [
                'connection_request_id' => $event->connectionRequestId,
            ]);
        }
    }
}
