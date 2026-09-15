<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification
{
    use Queueable;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
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
            'message_id' => $this->message->id,
        ];
    }
}
