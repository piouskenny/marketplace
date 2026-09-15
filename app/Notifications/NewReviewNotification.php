<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $reviewer = $this->review->reviewer;
        $reviewerName = $reviewer ? $reviewer->name : 'A user';

        return [
            'type' => 'new_review',
            'title' => 'New Rating & Review Received',
            'message' => "{$reviewerName} left you a {$this->review->rating}-star review!",
            'url' => url('/dashboard'),
            'icon' => 'star',
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
        ];
    }
}
