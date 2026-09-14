<?php

namespace App\Policies;

use App\Enums\ConnectionStatus;
use App\Models\Conversation;
use App\Models\User;

/**
 * Authorization policy for conversations and messages.
 *
 * Only users who are part of a connected (activated) connection
 * can access a conversation or send messages.
 */
class ConversationPolicy
{
    /**
     * Only connected participants of the connection can view the conversation.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        $connection = $conversation->connectionRequest;

        if (!$connection) {
            return false;
        }

        $statusValue = $connection->status instanceof ConnectionStatus 
            ? $connection->status->value 
            : (string) $connection->status;

        if ($statusValue !== ConnectionStatus::Connected->value) {
            return false;
        }

        return (int) $connection->initiator_id === (int) $user->id 
            || (int) $connection->recipient_id === (int) $user->id;
    }

    /**
     * Only connected participants can send messages.
     */
    public function sendMessage(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }
}
