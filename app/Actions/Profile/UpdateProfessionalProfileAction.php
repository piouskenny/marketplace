<?php

namespace App\Actions\Profile;

use App\Enums\TalentClassification;
use App\Models\Category;
use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Support\Str;

class UpdateProfessionalProfileAction
{
    public function __construct(
        private readonly SetUserTalentTypeAction $setTalentType
    ) {}

    /**
     * Create or update a base professional profile.
     *
     * @param  TalentClassification|null $forClassification
     *   The talent classification this profile is being created FOR.
     *   - TalentClassification::Professional → registers 'professional' in talent_type_user
     *   - TalentClassification::Teacher      → does NOT register 'professional'; Teacher
     *                                          classification is registered by UpdateEducationProfileAction
     *   - TalentClassification::SkilledLabour→ should not call this directly; use UpdateSkilledLabourProfileAction
     *   - null                               → no classification registered (base scaffold only)
     */
    public function execute(
        User $user,
        array $data,
        ?TalentClassification $forClassification = TalentClassification::Professional
    ): ProfessionalProfile {
        $category = Category::findOrFail($data['category_id']);

        $profile = ProfessionalProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'category_id'            => $category->id,
                'display_name'           => $data['display_name'] ?? $user->name,
                'bio'                    => $data['bio'],
                'location'               => $data['location'] ?? $user->location ?? 'Nigeria',
                'location_state'         => $data['location_state'] ?? null,
                'location_city'          => $data['location_city'] ?? null,
                'location_neighbourhood' => $data['location_neighbourhood'] ?? null,
                'location_landmark'      => $data['location_landmark'] ?? null,
                'years_of_experience'    => $data['years_of_experience'] ?? 1,
                'phone'                  => $data['phone'] ?? $user->phone,
                'contact_email'          => $data['contact_email'] ?? $user->email,
                'availability_status'    => 'available',
            ]
        );

        // Sync skills if provided
        if (isset($data['skills']) && is_array($data['skills'])) {
            $profile->skills()->sync($data['skills']);
        }

        // Check if category is Education & Tutoring vertical
        $isEducationCategory = Str::contains(Str::lower($category->name), ['education', 'tutor', 'academic']);

        // Register the correct classification — only if explicitly provided
        // and only when this is the terminal step for that classification.
        if ($forClassification === TalentClassification::Professional) {
            $this->setTalentType->execute(
                $user,
                TalentClassification::Professional,
                // Mark complete only when this is NOT followed by an education sub-form
                markCompleted: !$isEducationCategory
            );
        }
        // For Teacher path: $forClassification = null here; UpdateEducationProfileAction
        // registers the Teacher classification after the education form is submitted.

        // Update user account state
        $user->update([
            'phone'             => $data['phone'] ?? $user->phone,
            'location'          => $data['location'] ?? $user->location,
            'onboarding_intent' => 'offer_services',
        ]);

        return $profile;
    }
}
