<?php

namespace App\Actions\Profile;

use App\Models\Category;
use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Support\Str;

class UpdateProfessionalProfileAction
{
    /**
     * Create or update a base professional profile.
     */
    public function execute(User $user, array $data): ProfessionalProfile
    {
        $category = Category::findOrFail($data['category_id']);

        $profile = ProfessionalProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'category_id' => $category->id,
                'display_name' => $data['display_name'] ?? $user->name,
                'bio' => $data['bio'],
                'location' => $data['location'] ?? $user->location ?? 'Nigeria',
                'years_of_experience' => $data['years_of_experience'] ?? 1,
                'phone' => $data['phone'] ?? $user->phone,
                'contact_email' => $data['contact_email'] ?? $user->email,
                'availability_status' => 'available',
            ]
        );

        // Sync skills if provided
        if (isset($data['skills']) && is_array($data['skills'])) {
            $profile->skills()->sync($data['skills']);
        }

        // Check if category is Education & Tutoring vertical
        $isEducationCategory = Str::contains(Str::lower($category->name), ['education', 'tutor', 'academic']);

        // Update user onboarding state
        $user->update([
            'phone' => $data['phone'] ?? $user->phone,
            'location' => $data['location'] ?? $user->location,
            'onboarding_intent' => 'offer_services',
            'onboarding_completed' => !$isEducationCategory, // if education, tutor step completes it
        ]);

        return $profile;
    }
}
