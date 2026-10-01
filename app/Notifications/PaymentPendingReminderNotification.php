<?php

namespace App\Notifications;

use App\Models\ConnectionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentPendingReminderNotification extends Notification
{
    use Queueable;

    public ConnectionRequest $connectionRequest;

    public function __construct(ConnectionRequest $connectionRequest)
    {
        $this->connectionRequest = $connectionRequest->loadMissing(['recipient', 'opportunity']);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $recipient = $this->connectionRequest->recipient;
        $recipientName = $recipient ? $recipient->name : 'the provider';
        $opportunity = $this->connectionRequest->opportunity;
        $opportunityTitle = $opportunity ? $opportunity->title : 'your opportunity';
        $targetUrl = url('/dashboard/messages?conn_id=' . $this->connectionRequest->id);

        return (new MailMessage)
            ->subject('Reminder: Connection Fee Payment Pending on Skill Link NG')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your connection request with {$recipientName} for \"{$opportunityTitle}\" was accepted, but the connection fee payment has not been completed.")
            ->action('Complete Payment on Skill Link NG', $targetUrl)
            ->line('Thank you for using Skill Link NG!');
    }

    public function toArray(object $notifiable): array
    {
        $recipient = $this->connectionRequest->recipient;
        $recipientName = $recipient ? $recipient->name : 'the provider';

        return [
            'type' => 'payment_pending_reminder',
            'title' => 'Payment Pending Reminder',
            'message' => "Your connection with {$recipientName} is waiting for payment.",
            'url' => url('/dashboard/messages?conn_id=' . $this->connectionRequest->id),
            'icon' => 'credit-card',
            'connection_request_id' => $this->connectionRequest->id,
            'recipient_id' => $this->connectionRequest->recipient_id,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
