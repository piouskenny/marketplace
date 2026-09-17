<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public ?string $clientMsgId;

    public function __construct(Message $message, ?string $clientMsgId = null)
    {
        $this->message = $message->load(['sender', 'conversation']);
        $this->clientMsgId = $clientMsgId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $connId = $this->message->conversation ? $this->message->conversation->connection_request_id : null;

        return [
            'id' => $this->message->id,
            'client_msg_id' => $this->clientMsgId,
            'conversation_id' => $this->message->conversation_id,
            'connection_id' => $connId,
            'connection_request_id' => $connId,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender ? $this->message->sender->name : 'User',
            'body' => $this->message->body,
            'read_at' => $this->message->read_at ? $this->message->read_at->toIso8601String() : null,
            'created_at' => $this->message->created_at ? $this->message->created_at->toIso8601String() : now()->toIso8601String(),
            'time_formatted' => $this->message->created_at ? $this->message->created_at->toIso8601String() : now()->toIso8601String(),
        ];
    }
}
