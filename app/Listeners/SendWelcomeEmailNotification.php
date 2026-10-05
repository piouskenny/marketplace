<?php

namespace App\Listeners;

use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailNotification
{
    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        try {
            /** @var \App\Models\User $user */
            $user = $event->user;

            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send WelcomeMail: ' . $e->getMessage(), [
                'user_id' => $event->user?->id,
            ]);
        }
    }
}
