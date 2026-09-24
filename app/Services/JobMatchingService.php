<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Support\Collection;

class JobMatchingService
{
    /**
     * Minimum score required to trigger a job opportunity email alert.
     */
    public const MATCH_THRESHOLD = 40;

    /**
     * Find all eligible and matching professionals for a given published opportunity.
     *
     * @param Opportunity $opportunity
     * @return Collection<int, User>
     */
    public function getMatchingProfessionals(Opportunity $opportunity): Collection
    {
        // Load opportunity relationships
        $opportunity->loadMissing(['category', 'educationDetails.subject', 'educationDetails.educationLevel']);

        $oppCatName = strtolower($opportunity->category->name ?? '');
        $isTutoring = str_contains($oppCatName, 'education') || str_contains($oppCatName, 'tutor');
        $oppTitleLower = strtolower($opportunity->title);
        $oppDescLower = strtolower($opportunity->description);
        $oppLocLower = strtolower($opportunity->location);
        $isRemoteJob = str_contains(strtolower($opportunity->opportunity_type), 'online')
                    || str_contains(strtolower($opportunity->opportunity_type), 'remote')
                    || str_contains($oppLocLower, 'online')
                    || str_contains($oppLocLower, 'remote');

        $eduDetails = $opportunity->educationDetails;
        $oppSubjectId = $eduDetails?->subject_id;
        $oppLevelId = $eduDetails?->education_level_id;
        $oppSubjectName = strtolower($eduDetails?->subject?->name ?? '');

        // 1. Fetch candidate pool (onboarded, email verified, opted-in, non-author)
        $candidates = User::query()
            ->where('onboarding_completed', true)
            ->whereNotNull('email_verified_at')
            ->where('job_alerts_enabled', true)
            ->where('id', '!=', $opportunity->user_id)
            ->with([
                'professionalProfile.category',
                'professionalProfile.skills',
                'professionalProfile.educationProfile.subjects',
                'professionalProfile.educationProfile.educationLevels',
            ])
            ->get();

        $matchingUsers = collect();

        foreach ($candidates as $user) {
            $score = 0;
            $profile = $user->professionalProfile;
            if (!$profile) {
                // Skip users without a professional profile
                continue;
            }

            // 1. Category Matching (Max 35 points)
            if ($profile->category_id && (int) $profile->category_id === (int) $opportunity->category_id) {
                $score += 35;
            } elseif (!empty($profile->category?->name)) {
                $userCatName = strtolower($profile->category->name);
                if (str_contains($oppCatName, $userCatName) || str_contains($userCatName, $oppCatName)) {
                    $score += 25;
                }
            } elseif (!empty($user->onboarding_intent)) {
                $intent = strtolower($user->onboarding_intent);
                if (str_contains($oppCatName, $intent) || str_contains($intent, $oppCatName)) {
                    $score += 20;
                }
            }

            // 2. Academic Tutoring & Subject Matching (Max 30 points)
            if ($isTutoring || $oppSubjectId || $eduDetails) {
                $eduProfile = $profile->educationProfile;
                if ($eduProfile) {
                    // Subject match
                    if ($oppSubjectId && $eduProfile->subjects) {
                        $userSubjectIds = $eduProfile->subjects->pluck('id')->toArray();
                        if (in_array((int) $oppSubjectId, array_map('intval', $userSubjectIds), true)) {
                            $score += 20;
                        }
                    } elseif ($oppSubjectName && $eduProfile->subjects) {
                        $userSubjectNames = $eduProfile->subjects->pluck('name')->map(fn($s) => strtolower(trim($s)))->toArray();
                        if (in_array($oppSubjectName, $userSubjectNames, true)) {
                            $score += 20;
                        }
                    }

                    // Education level match
                    if ($oppLevelId && $eduProfile->educationLevels) {
                        $userLevelIds = $eduProfile->educationLevels->pluck('id')->toArray();
                        if (in_array((int) $oppLevelId, array_map('intval', $userLevelIds), true)) {
                            $score += 10;
                        }
                    }
                }
            }

            // 3. Location & Teaching Mode Matching (Max 25 points)
            $userLocLower = strtolower(trim($user->location ?? $profile->location ?? ''));
            if ($isRemoteJob) {
                $score += 15;
            } elseif (!empty($userLocLower) && !empty($oppLocLower)) {
                if (str_contains($oppLocLower, $userLocLower) || str_contains($userLocLower, $oppLocLower)) {
                    $score += 25;
                }
            }

            // 4. Skills & Bio Keyword Matching (Max 20 points)
            $userSkills = $profile->skills
                ? $profile->skills->pluck('name')->map(fn($s) => strtolower(trim($s)))->toArray()
                : [];
            
            $skillMatched = false;
            foreach ($userSkills as $skill) {
                if (!empty($skill) && (str_contains($oppTitleLower, $skill) || str_contains($oppDescLower, $skill) || str_contains($oppSubjectName, $skill))) {
                    $score += 20;
                    $skillMatched = true;
                    break;
                }
            }

            if (!$skillMatched && !empty($profile->bio)) {
                $bioLower = strtolower($profile->bio);
                if (!empty($oppSubjectName) && str_contains($bioLower, $oppSubjectName)) {
                    $score += 10;
                }
            }

            // Verify if candidate exceeds match score threshold
            if ($score >= self::MATCH_THRESHOLD) {
                $matchingUsers->push($user);
            }
        }

        return $matchingUsers;
    }
}
