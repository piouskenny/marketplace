<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PendingRequestReminderNotification extends Notification
{
    use Queueable;

    public ConnectionRequest $connectionRequest;

    public function __construct(ConnectionRequest $connectionRequest)
    {
        $this->connectionRequest = $connectionRequest->loadMissing(['initiator', 'opportunity']);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $initiator = $this->connectionRequest->initiator;
        $initiatorName = $initiator ? $initiator->name : 'A user';
        $opportunity = $this->connectionRequest->opportunity;
        $opportunityTitle = $opportunity ? $opportunity->title : 'your opportunity';
        $targetUrl = url('/dashboard/messages?conn_id=' . $this->connectionRequest->id);

        return (new MailMessage)
            ->subject('Reminder: Pending Connection Request on Skill Link NG')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("{$initiatorName} sent you a connection request for \"{$opportunityTitle}\" that is waiting for your response.")
            ->action('Review Request on Skill Link NG', $targetUrl)
            ->line('Thank you for using Skill Link NG!');
    }

    public function toArray(object $notifiable): array
    {
        $initiator = $this->connectionRequest->initiator;
        $initiatorName = $initiator ? $initiator->name : 'A user';
        $opportunity = $this->connectionRequest->opportunity;
        $opportunityTitle = $opportunity ? $opportunity->title : 'Opportunity Request';

        return [
            'type' => 'pending_request_reminder',
            'title' => 'Pending Connection Request Reminder',
            'message' => "{$initiatorName}'s request for '{$opportunityTitle}' is still waiting for your response.",
            'url' => url('/dashboard/messages?conn_id=' . $this->connectionRequest->id),
            'icon' => 'user-plus',
            'connection_request_id' => $this->connectionRequest->id,
            'initiator_id' => $this->connectionRequest->initiator_id,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
