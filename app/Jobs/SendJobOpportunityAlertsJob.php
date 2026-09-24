<?php

namespace App\Jobs;

use App\Models\Opportunity;
use App\Notifications\JobOpportunityAlertNotification;
use App\Services\JobMatchingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendJobOpportunityAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Opportunity $opportunity;

    /**
     * Create a new job instance.
     */
    public function __construct(Opportunity $opportunity)
    {
        $this->opportunity = $opportunity;
    }

    /**
     * Execute the job.
     */
    public function handle(JobMatchingService $matchingService): void
    {
        try {
            $matchingUsers = $matchingService->getMatchingProfessionals($this->opportunity);

            Log::info("[JobAlertJob] Found {$matchingUsers->count()} matching professionals for opportunity ID {$this->opportunity->id}");

            foreach ($matchingUsers as $user) {
                // Deduplication check
                $cacheKey = "job_alert_sent_{$this->opportunity->id}_{$user->id}";
                if (Cache::has($cacheKey)) {
                    Log::info("[JobAlertJob] Skipping duplicate alert for User {$user->id} on Opportunity {$this->opportunity->id}");
                    continue;
                }

                Cache::put($cacheKey, true, now()->addDays(7));

                try {
                    $user->notify(new JobOpportunityAlertNotification($this->opportunity));
                    Log::info("[JobAlertJob] Dispatched JobOpportunityAlertNotification to User ID {$user->id} ({$user->email})");
                } catch (\Throwable $e) {
                    Log::error("[JobAlertJob] Failed to send notification to User ID {$user->id}: " . $e->getMessage(), [
                        'exception' => $e,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error("[JobAlertJob] Error running job matching for Opportunity ID {$this->opportunity->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
