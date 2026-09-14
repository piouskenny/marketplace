<?php

use App\Enums\ConnectionStatus;
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::with('connectionRequest')->find($conversationId);
    if (!$conversation || !$conversation->connectionRequest) {
        return false;
    }

    $connection = $conversation->connectionRequest;
    $statusValue = $connection->status instanceof ConnectionStatus 
        ? $connection->status->value 
        : (string) $connection->status;

    if ($statusValue !== ConnectionStatus::Connected->value) {
        return false;
    }

    return (int) $connection->initiator_id === (int) $user->id 
        || (int) $connection->recipient_id === (int) $user->id;
});
