<?php

namespace App\Actions\Profile;

use App\Enums\TalentClassification;
use App\Models\TalentType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Add or mark-complete a talent classification for a user.
 *
 * This is the single place that writes to the talent_type_user pivot.
 * It must be called explicitly by the relevant onboarding actions — never
 * inferred automatically from the presence of a professional_profiles record.
 */
class SetUserTalentTypeAction
{
    /**
     * Register a talent classification for the user.
     *
     * @param  User                 $user
     * @param  TalentClassification $classification
     * @param  bool                 $markCompleted  Set completed_at = now() when the
     *                                              full sub-form has been submitted.
     *                                              Pass false when the user has merely
     *                                              selected the classification but has
     *                                              not yet finished the detail form.
     */
    public function execute(User $user, TalentClassification $classification, bool $markCompleted = true): void
    {
        DB::transaction(function () use ($user, $classification, $markCompleted) {
            $talentType = TalentType::where('slug', $classification->value)->firstOrFail();

            $pivotData = $markCompleted
                ? ['completed_at' => now()]
                : ['completed_at' => null];

            // syncWithoutDetaching preserves other existing classifications
            $user->talentTypes()->syncWithoutDetaching([
                $talentType->id => $pivotData,
            ]);

            // If already in pivot but completed_at needs updating
            if ($markCompleted) {
                $user->talentTypes()->updateExistingPivot($talentType->id, [
                    'completed_at' => now(),
                ]);
            }

            // Stamp talent onboarding start time if not already set
            if (!$user->talent_onboarding_started_at) {
                $user->update(['talent_onboarding_started_at' => now()]);
            }
        });
    }
}
