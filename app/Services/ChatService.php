<?php

namespace App\Services;

/**
 * Service for sending and reading chat messages within a conversation.
 *
 * Handles message persistence, read-state tracking, and broadcasting.
 * Authorization (is this user part of this conversation?) is enforced
 * by ConversationPolicy — this service assumes the caller is authorized.
 */
class ChatService
{
    /**
     * Send a message in a conversation.
     *
     * Placeholder — will persist the message, broadcast via Pusher,
     * and dispatch a NewMessageSent event.
     */
    public function sendMessage(int $conversationId, int $senderId, string $body): mixed
    {
        throw new \RuntimeException('ChatService::sendMessage is not yet implemented.');
    }

    /**
     * Mark messages as read for a given user in a conversation.
     */
    public function markAsRead(int $conversationId, int $userId): void
    {
        throw new \RuntimeException('ChatService::markAsRead is not yet implemented.');
    }
}
