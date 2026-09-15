<?php

namespace App\Policies;

use App\Enums\ConnectionStatus;
use App\Models\ConnectionRequest;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can create a review for the connection request.
     */
    public function create(User $user, ConnectionRequest $connectionRequest): bool
    {
        if ($connectionRequest->status !== ConnectionStatus::Connected) {
            return false;
        }

        $isParticipant = ($connectionRequest->initiator_id === $user->id || $connectionRequest->recipient_id === $user->id);
        if (!$isParticipant) {
            return false;
        }

        $alreadyReviewed = Review::where('connection_request_id', $connectionRequest->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        return !$alreadyReviewed;
    }
}
