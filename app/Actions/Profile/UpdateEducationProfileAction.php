<?php

namespace App\Actions\Profile;

use App\Enums\TalentClassification;
use App\Models\EducationProfile;
use App\Models\ProfessionalProfile;
use App\Models\User;

class UpdateEducationProfileAction
{
    public function __construct(
        private readonly SetUserTalentTypeAction $setTalentType
    ) {}

    /**
     * Create or update specialized education profile metadata.
     *
     * Registers the Teacher classification in talent_type_user upon completion.
     * Does NOT register the Professional classification — a Teacher who also
     * holds the Professional classification must add it separately.
     */
    public function execute(User $user, array $data): EducationProfile
    {
        $professionalProfile = $user->professionalProfile()->first();

        if (!$professionalProfile) {
            // Fallback: create a minimal base profile if missing.
            // This is an internal infrastructure requirement for the FK — it does NOT
            // mean the user is classified as "Professional" in talent_type_user.
            $professionalProfile = ProfessionalProfile::create([
                'user_id'      => $user->id,
                'category_id'  => $data['category_id'] ?? 1,
                'display_name' => $user->name,
                'bio'          => 'Academic tutor offering tutoring services.',
                'location'     => $user->location ?? 'Nigeria',
            ]);
        }

        $teachingMode = $data['teaching_mode'] ?? 'both';
        if (is_object($teachingMode) && method_exists($teachingMode, 'value')) {
            $teachingMode = $teachingMode->value;
        }

        $educationProfile = EducationProfile::updateOrCreate(
            ['professional_profile_id' => $professionalProfile->id],
            [
                'teaching_mode'  => $teachingMode,
                'qualifications' => $data['qualifications'] ?? null,
                'rate_min'       => isset($data['rate_min']) ? (int) $data['rate_min'] * 100 : null,
                'rate_max'       => isset($data['rate_max']) ? (int) $data['rate_max'] * 100 : null,
            ]
        );

        // Sync subjects
        if (isset($data['subject_ids']) && is_array($data['subject_ids'])) {
            $educationProfile->subjects()->sync($data['subject_ids']);
        }

        // Sync education levels
        if (isset($data['level_ids']) && is_array($data['level_ids'])) {
            $educationProfile->educationLevels()->sync($data['level_ids']);
        }

        // Register Teacher classification (not Professional) and mark complete
        $this->setTalentType->execute($user, TalentClassification::Teacher, markCompleted: true);

        $user->update([
            'onboarding_intent' => 'offer_services',
        ]);

        return $educationProfile;
    }
}
