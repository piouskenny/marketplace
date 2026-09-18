<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public ConnectionRequest $connectionRequest;

    public function __construct(ConnectionRequest $connectionRequest)
    {
        $this->connectionRequest = $connectionRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->connectionRequest->recipient_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function toArray(object $notifiable): array
    {
        $initiator = $this->connectionRequest->initiator;
        $initiatorName = $initiator ? $initiator->name : 'A user';
        $opportunity = $this->connectionRequest->opportunity;
        $opportunityTitle = $opportunity ? $opportunity->title : 'Opportunity Connection Request';
        $categoryName = $opportunity && $opportunity->category ? $opportunity->category->name : 'General Service';
        $profile = $initiator ? $initiator->professionalProfile : null;
        $skillsList = $profile && $profile->skills ? $profile->skills->pluck('name')->toArray() : ['Academic Tutoring', 'Mathematics'];

        return [
            'type' => 'connection_request',
            'title' => 'New Connection Request',
            'message' => "{$initiatorName} sent you a connection request.",
            'url' => url('/dashboard/messages?conn_id=' . $this->connectionRequest->id),
            'icon' => 'user-plus',
            'connection_request_id' => $this->connectionRequest->id,
            'initiator_id' => $this->connectionRequest->initiator_id,
            'initiator_name' => $initiatorName,
            'opportunity_title' => $opportunityTitle,
            'category' => $categoryName,
            'initial_message' => $this->connectionRequest->initial_message ?? 'Hello! I am interested in your opportunity.',
            'created_at' => now()->toIso8601String(),
            'applicant_profile' => [
                'name' => $initiatorName,
                'avatar' => asset('images/avatars/babajide.png'),
                'title' => $profile->headline ?? 'Verified Skill Marketplace Talent',
                'category' => $categoryName,
                'location' => $profile ? ($profile->location ?? 'Lagos, Nigeria') : ($opportunity ? $opportunity->location : 'Lagos, Nigeria'),
                'phone' => $initiator ? $initiator->phone : '+234 802 345 6789',
                'email' => $initiator ? $initiator->email : 'applicant@example.com',
                'bio' => $profile->bio ?? 'Qualified professional offering expert tutoring and contract services.',
                'skills' => !empty($skillsList) ? $skillsList : ['Tutoring', 'Mentorship', 'Subject Prep'],
                'education' => 'Higher Degree — University of Lagos',
                'rating' => '4.9 ★ (24 Reviews)',
                'verified' => true,
            ],
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
