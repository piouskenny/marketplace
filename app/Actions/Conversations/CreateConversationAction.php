<?php

namespace App\Actions\Conversations;

use App\Models\ConnectionRequest;
use App\Models\Conversation;

class CreateConversationAction
{
    /**
     * Idempotently create or retrieve a conversation for a connection request.
     */
    public function execute(ConnectionRequest $connectionRequest): Conversation
    {
        return Conversation::firstOrCreate([
            'connection_request_id' => $connectionRequest->id,
        ]);
    }
}
