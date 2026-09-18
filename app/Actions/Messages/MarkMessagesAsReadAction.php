<?php

namespace App\Actions\Messages;

use App\Events\MessagesRead;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class MarkMessagesAsReadAction
{
    /**
     * Mark incoming unread messages as read for the viewing user and dispatch realtime broadcast.
     */
    public function execute(Conversation $conversation, User $user): int
    {
        $unreadIds = Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->pluck('id')
            ->toArray();

        if (empty($unreadIds)) {
            return 0;
        }

        $now = now();

        $updatedCount = Message::whereIn('id', $unreadIds)
            ->update([
                'read_at' => $now,
            ]);

        event(new MessagesRead(
            $conversation->id,
            $user->id,
            $now->toIso8601String(),
            $unreadIds
        ));

        return $updatedCount;
    }
}

