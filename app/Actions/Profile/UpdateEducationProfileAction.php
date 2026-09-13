<?php

namespace App\Actions\Profile;

use App\Models\EducationProfile;
use App\Models\ProfessionalProfile;
use App\Models\User;

class UpdateEducationProfileAction
{
    /**
     * Create or update specialized education profile metadata.
     */
    public function execute(User $user, array $data): EducationProfile
    {
        $professionalProfile = $user->professionalProfile;

        if (!$professionalProfile) {
            // Fallback: create base pro profile if missing
            $professionalProfile = ProfessionalProfile::create([
                'user_id' => $user->id,
                'category_id' => $data['category_id'] ?? 1,
                'display_name' => $user->name,
                'bio' => 'Academic tutor offering tutoring services.',
                'location' => $user->location ?? 'Nigeria',
            ]);
        }

        $teachingMode = $data['teaching_mode'] ?? 'both';
        if (is_object($teachingMode) && method_exists($teachingMode, 'value')) {
            $teachingMode = $teachingMode->value;
        }

        $educationProfile = EducationProfile::updateOrCreate(
            ['professional_profile_id' => $professionalProfile->id],
            [
                'teaching_mode' => $teachingMode,
                'qualifications' => $data['qualifications'] ?? null,
                'rate_min' => isset($data['rate_min']) ? (int)$data['rate_min'] * 100 : null, // Store in kobo/cents
                'rate_max' => isset($data['rate_max']) ? (int)$data['rate_max'] * 100 : null,
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

        // Mark onboarding complete
        $user->update([
            'onboarding_completed' => true,
            'onboarding_intent' => 'offer_services',
        ]);

        return $educationProfile;
    }
}
