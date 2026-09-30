<?php

namespace App\Actions\Profile;

use App\Enums\TalentClassification;
use App\Models\Category;
use App\Models\ProfessionalProfile;
use App\Models\SkilledLabourProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Create or update a Skilled Labour specialisation profile.
 *
 * Flow:
 *  1. Ensure a base professional_profiles record exists (required for the FK).
 *     Creating the base record here does NOT register the Professional classification.
 *  2. Create/update skilled_labour_profiles with trade-specific data.
 *  3. Register the skilled_labour talent classification via SetUserTalentTypeAction.
 *  4. Mark user onboarding as complete.
 */
class UpdateSkilledLabourProfileAction
{
    public function __construct(
        private readonly SetUserTalentTypeAction $setTalentType
    ) {}

    /**
     * @param  User  $user
     * @param  array{
     *     trade_category_id: int|null,
     *     is_certified: bool,
     *     certification_notes: string|null,
     *     display_name?: string,
     *     bio?: string,
     *     location?: string,
     *     location_state?: string,
     *     location_city?: string,
     *     location_neighbourhood?: string,
     *     location_landmark?: string,
     *     years_of_experience?: int,
     *     phone?: string,
     *     skills?: array<int>,
     * } $data
     */
    public function execute(User $user, array $data): SkilledLabourProfile
    {
        return DB::transaction(function () use ($user, $data) {
            // 1. Ensure base professional_profiles record exists.
            //    This is an internal infrastructure requirement — NOT a Professional classification.
            $baseProfile = $user->professionalProfile()->first() ?? ProfessionalProfile::create([
                'user_id'      => $user->id,
                'category_id'  => $data['trade_category_id'] ?? Category::first()?->id ?? 1,
                'display_name' => $data['display_name'] ?? $user->name,
                'bio'          => $data['bio'] ?? 'Skilled trade worker on Skill Link NG.',
                'location'     => $data['location'] ?? $user->location ?? 'Nigeria',
                'availability_status' => 'available',
            ]);

            // 2. Update base profile with any shared fields provided
            $baseUpdates = array_filter([
                'display_name'         => $data['display_name'] ?? null,
                'bio'                  => $data['bio'] ?? null,
                'location'             => $data['location'] ?? null,
                'location_state'       => $data['location_state'] ?? null,
                'location_city'        => $data['location_city'] ?? null,
                'location_neighbourhood' => $data['location_neighbourhood'] ?? null,
                'location_landmark'    => $data['location_landmark'] ?? null,
                'years_of_experience'  => $data['years_of_experience'] ?? null,
                'phone'                => $data['phone'] ?? null,
            ], fn($v) => $v !== null);

            if (!empty($baseUpdates)) {
                $baseProfile->update($baseUpdates);
            }

            // Sync skills via the existing professional_profile_skill pivot
            if (isset($data['skills']) && is_array($data['skills'])) {
                $baseProfile->skills()->sync($data['skills']);
            }

            // 3. Create/update the skilled labour specialisation record
            $skilledLabourProfile = SkilledLabourProfile::updateOrCreate(
                ['professional_profile_id' => $baseProfile->id],
                [
                    'trade_category_id'   => $data['trade_category_id'] ?? null,
                    'is_certified'        => $data['is_certified'] ?? false,
                    'certification_notes' => $data['certification_notes'] ?? null,
                ]
            );

            // 4. Register skilled_labour classification (not professional)
            $this->setTalentType->execute($user, TalentClassification::SkilledLabour, markCompleted: true);

            $user->update([
                'onboarding_intent' => 'offer_services',
                'phone'             => $data['phone'] ?? $user->phone,
                'location'          => $data['location'] ?? $user->location,
            ]);

            return $skilledLabourProfile;
        });
    }
}
