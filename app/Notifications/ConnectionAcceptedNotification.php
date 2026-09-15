<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionAcceptedNotification extends Notification
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
            'type' => 'connection_accepted',
            'title' => 'Connection Accepted — Payment Required',
            'message' => "{$recipientName} accepted your connection request! Pay ₦1,000 connection fee to activate direct chat.",
            'url' => url('/dashboard/messages?conn_id=' . $this->connectionRequest->id),
            'icon' => 'check-circle',
            'connection_request_id' => $this->connectionRequest->id,
        ];
    }
}
