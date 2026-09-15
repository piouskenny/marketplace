<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ConnectionActivatedNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public ConnectionRequest $connectionRequest;
    public ?int $targetUserId;

    public function __construct(ConnectionRequest $connectionRequest, ?int $targetUserId = null)
    {
        $this->connectionRequest = $connectionRequest;
        $this->targetUserId = $targetUserId;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function broadcastOn(): array
    {
        $userId = $this->targetUserId ?? $this->connectionRequest->initiator_id;
        return [
            new PrivateChannel('user.' . $userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function toArray(object $notifiable): array
    {
        $this->connectionRequest->loadMissing('conversation');
        $convId = $this->connectionRequest->conversation ? $this->connectionRequest->conversation->id : null;

        return [
            'type' => 'connection_activated',
            'title' => 'Connection Activated!',
            'message' => "Payment verified! Direct chat and private contact details are now unlocked.",
            'url' => url('/dashboard/messages?conn_id=' . $this->connectionRequest->id),
            'icon' => 'unlock',
            'connection_request_id' => $this->connectionRequest->id,
            'connection_id' => $this->connectionRequest->id,
            'conversation_id' => $convId,
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
