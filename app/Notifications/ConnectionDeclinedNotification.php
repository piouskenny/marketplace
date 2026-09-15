<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionDeclinedNotification extends Notification
{
    use Queueable;

    public ConnectionRequest $connectionRequest;

    public function __construct(ConnectionRequest $connectionRequest)
    {
        $this->connectionRequest = $connectionRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $recipient = $this->connectionRequest->recipient;
        $recipientName = $recipient ? $recipient->name : 'The recipient';

        return [
            'type' => 'connection_declined',
            'title' => 'Connection Declined',
            'message' => "{$recipientName} declined your connection request.",
            'url' => url('/dashboard/messages'),
            'icon' => 'x-circle',
            'connection_request_id' => $this->connectionRequest->id,
        ];
    }
}
