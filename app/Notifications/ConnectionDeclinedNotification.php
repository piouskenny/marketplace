<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ConnectionDeclinedNotification extends Notification implements ShouldBroadcastNow
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
            new PrivateChannel('user.' . $this->connectionRequest->initiator_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
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
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
