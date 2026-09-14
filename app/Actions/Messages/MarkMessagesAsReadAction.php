<?php

namespace App\Actions\Messages;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class MarkMessagesAsReadAction
{
    /**
     * Mark incoming unread messages as read for the viewing user.
     */
    public function execute(Conversation $conversation, User $user): int
    {
        return Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }
}
