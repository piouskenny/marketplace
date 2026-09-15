<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionRequestNotification extends Notification
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
        $initiator = $this->connectionRequest->initiator;
        $initiatorName = $initiator ? $initiator->name : 'A user';

        return [
            'type' => 'connection_request',
            'title' => 'New Connection Request',
            'message' => "{$initiatorName} sent you a connection request.",
            'url' => url('/dashboard/messages?conn_id=' . $this->connectionRequest->id),
            'icon' => 'user-plus',
            'connection_request_id' => $this->connectionRequest->id,
            'initiator_id' => $this->connectionRequest->initiator_id,
        ];
    }
}
