<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public ConnectionRequest $connectionRequest;

    public function __construct(ConnectionRequest $connectionRequest)
    {
        $this->connectionRequest = $connectionRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->connectionRequest->recipient_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
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
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
