<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionActivatedNotification extends Notification
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
        return [
            'type' => 'connection_activated',
            'title' => 'Connection Activated!',
            'message' => "Payment verified! Direct chat and private contact details are now unlocked.",
            'url' => url('/dashboard/messages?conn_id=' . $this->connectionRequest->id),
            'icon' => 'unlock',
            'connection_request_id' => $this->connectionRequest->id,
        ];
    }
}
