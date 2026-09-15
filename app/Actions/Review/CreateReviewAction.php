<?php

namespace App\Actions\Review;

use App\Enums\ConnectionStatus;
use App\Models\ConnectionRequest;
use App\Models\ProfessionalProfile;
use App\Models\Review;
use App\Notifications\NewReviewNotification;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateReviewAction
{
    /**
     * Create a review for an activated connection request.
     *
     * @param ConnectionRequest $connectionRequest
     * @param int $reviewerId
     * @param int $rating
     * @param string|null $comment
     * @return Review
     */
    public function execute(ConnectionRequest $connectionRequest, int $reviewerId, int $rating, ?string $comment = null): Review
    {
        // 1. Verify connection status is Connected
        if ($connectionRequest->status !== ConnectionStatus::Connected) {
            throw new InvalidArgumentException('Reviews can only be submitted for active, connected requests.');
        }

        // 2. Verify reviewer is a participant in this connection
        if ($connectionRequest->initiator_id !== $reviewerId && $connectionRequest->recipient_id !== $reviewerId) {
            throw new InvalidArgumentException('You are not a participant in this connection.');
        }

        // 3. Determine reviewee
        $revieweeId = ($connectionRequest->initiator_id === $reviewerId)
            ? $connectionRequest->recipient_id
            : $connectionRequest->initiator_id;

        if ($reviewerId === $revieweeId) {
            throw new InvalidArgumentException('You cannot review yourself.');
        }

        // 4. Validate rating bounds
        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('Rating must be an integer between 1 and 5.');
        }

        // 5. Prevent duplicate review by same reviewer for same connection
        $existingReview = Review::where('connection_request_id', $connectionRequest->id)
            ->where('reviewer_id', $reviewerId)
            ->first();

        if ($existingReview) {
            throw new InvalidArgumentException('You have already submitted a review for this connection.');
        }

        return DB::transaction(function () use ($connectionRequest, $reviewerId, $revieweeId, $rating, $comment) {
            $review = Review::create([
                'connection_request_id' => $connectionRequest->id,
                'reviewer_id' => $reviewerId,
                'reviewee_id' => $revieweeId,
                'rating' => $rating,
                'comment' => $comment,
            ]);

            // Update average rating and reviews count on ProfessionalProfile if exists
            $profile = ProfessionalProfile::where('user_id', $revieweeId)->first();
            if ($profile) {
                $avgRating = Review::where('reviewee_id', $revieweeId)->avg('rating') ?: 0;
                $count = Review::where('reviewee_id', $revieweeId)->count();

                $profile->update([
                    'average_rating' => round($avgRating, 2),
                    'reviews_count' => $count,
                ]);
            }

            // Dispatch notification to reviewee if reviewee user exists
            $reviewee = $review->reviewee;
            if ($reviewee) {
                $reviewee->notify(new NewReviewNotification($review));
            }

            return $review;
        });
    }
}
