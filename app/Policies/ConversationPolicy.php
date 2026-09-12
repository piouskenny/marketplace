<?php

namespace App\Policies;

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
     * Only participants of the connection can view the conversation.
     */
    public function view(User $user, mixed $conversation): bool
    {
        // Placeholder — will check that the user is either the initiator
        // or recipient of the associated ConnectionRequest AND that the
        // connection status is Connected.
        return false;
    }

    /**
     * Only participants can send messages.
     */
    public function sendMessage(User $user, mixed $conversation): bool
    {
        return false;
    }
}
