<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public Message $message;
    public ?int $targetUserId = null;

    public function __construct(Message $message)
    {
        $this->message = $message->loadMissing(['conversation.connectionRequest', 'sender']);
        $conn = $this->message->conversation ? $this->message->conversation->connectionRequest : null;
        if ($conn) {
            $this->targetUserId = ($conn->initiator_id === $this->message->sender_id)
                ? $conn->recipient_id
                : $conn->initiator_id;
        }
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function broadcastOn(): array
    {
        $userId = $this->targetUserId;
        if (!$userId && isset($this->message->conversation) && $this->message->conversation->connectionRequest) {
            $conn = $this->message->conversation->connectionRequest;
            $userId = ($conn->initiator_id === $this->message->sender_id)
                ? $conn->recipient_id
                : $conn->initiator_id;
        }

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
        $sender = $this->message->sender;
        $senderName = $sender ? $sender->name : 'Someone';
        $bodyPreview = Str::limit($this->message->body, 50);
        $connId = $this->message->conversation ? $this->message->conversation->connection_request_id : null;

        return [
            'type' => 'new_message',
            'title' => "New message from {$senderName}",
            'message' => $bodyPreview,
            'url' => url('/dashboard/messages' . ($connId ? '?conn_id=' . $connId : '')),
            'icon' => 'message-square',
            'conversation_id' => $this->message->conversation_id,
            'connection_request_id' => $connId,
            'connection_id' => $connId,
            'message_id' => $this->message->id,
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
