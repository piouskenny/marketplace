<?php

namespace App\Actions\Messages;

use App\Enums\ConnectionStatus;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SendMessageAction
{
    /**
     * Send a message within a conversation.
     * Enforces the invariant that the underlying connection MUST be Connected.
     */
    public function execute(Conversation $conversation, User $sender, string $body, ?string $clientMsgId = null): Message
    {
        $connection = $conversation->connectionRequest;

        if (!$connection) {
            throw ValidationException::withMessages([
                'conversation' => ['Connection request associated with conversation not found.'],
            ]);
        }

        $statusValue = $connection->status instanceof ConnectionStatus 
            ? $connection->status->value 
            : (string) $connection->status;

        if ($statusValue !== ConnectionStatus::Connected->value) {
            throw ValidationException::withMessages([
                'conversation' => ['Messaging is disabled because connection is not in Connected status.'],
            ]);
        }

        $trimmedBody = trim($body);
        if (empty($trimmedBody)) {
            throw ValidationException::withMessages([
                'body' => ['Message body cannot be empty.'],
            ]);
        }

        // 1. Save message to MySQL
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $trimmedBody,
            'read_at' => null,
        ]);

        // 2. Update last_message_at on Conversation
        $conversation->update([
            'last_message_at' => now(),
        ]);

        // 3. Broadcast real-time event after MySQL persistence
        event(new MessageSent($message, $clientMsgId));

        // 4. Send database notification to recipient
        $recipient = ($connection->initiator_id === $sender->id)
            ? $connection->recipient
            : $connection->initiator;

        if ($recipient) {
            $recipient->notify(new \App\Notifications\NewMessageNotification($message, $clientMsgId));
        }

        return $message;
    }
}
