<?php

namespace App\Notifications;

use App\Models\Opportunity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class JobOpportunityAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Opportunity $opportunity;

    /**
     * Create a new notification instance.
     */
    public function __construct(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->opportunity->loadMissing(['category', 'educationDetails.subject', 'educationDetails.educationLevel', 'user']);

        $unsubscribeUrl = URL::signedRoute('job-alerts.unsubscribe', ['user' => $notifiable->id]);
        $opportunityUrl = url('/dashboard?job_id=' . $this->opportunity->id);

        $budgetText = 'Negotiable';
        if ($this->opportunity->budget_min && $this->opportunity->budget_max) {
            $budgetText = '₦' . number_format($this->opportunity->budget_min) . ' - ₦' . number_format($this->opportunity->budget_max);
        } elseif ($this->opportunity->budget_min) {
            $budgetText = '₦' . number_format($this->opportunity->budget_min);
        }

        return (new MailMessage)
            ->subject('New Opportunity Match: ' . $this->opportunity->title)
            ->view('emails.job-opportunity-alert', [
                'user' => $notifiable,
                'opportunity' => $this->opportunity,
                'budgetText' => $budgetText,
                'opportunityUrl' => $opportunityUrl,
                'unsubscribeUrl' => $unsubscribeUrl,
            ]);
    }

    /**
     * Get the array representation of the notification for database persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'job_opportunity_alert',
            'opportunity_id' => $this->opportunity->id,
            'title' => $this->opportunity->title,
            'category' => $this->opportunity->category->name ?? 'General Opportunity',
            'location' => $this->opportunity->location,
            'message' => 'New opportunity posted matching your profile: "' . $this->opportunity->title . '"',
            'url' => url('/dashboard?job_id=' . $this->opportunity->id),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
