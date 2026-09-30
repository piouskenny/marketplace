<?php

namespace App\Console\Commands;

use App\Enums\TalentClassification;
use App\Models\Category;
use App\Models\ProfessionalProfile;
use App\Models\SkilledLabourProfile;
use App\Models\TalentType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillTalentClassifications extends Command
{
    protected $signature = 'app:backfill-talent-classifications
                            {--dry-run : Preview classifications without writing to database}';

    protected $description = 'Idempotent backfill of talent_type_user rows from existing professional_profiles category data';

    /**
     * Category parent slugs that map to Skilled Labour classification.
     * All children of Home & Technical Services are clearly trade occupations.
     */
    private const SKILLED_LABOUR_PARENT_SLUG = 'home-technical-services';

    /**
     * Category parent slugs that map to Teacher classification.
     */
    private const TEACHER_PARENT_SLUG = 'education-tutoring';

    /**
     * Category parent slugs that map to Professional classification.
     * Beauty & Lifestyle → Professional for V1 (documented decision).
     */
    private const PROFESSIONAL_PARENT_SLUGS = [
        'creative-digital-services',
        'business-professional',
        'beauty-lifestyle',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('[DRY RUN] No database changes will be written.');
        }

        // Pre-load all TalentType rows indexed by slug
        $talentTypes = TalentType::all()->keyBy('slug');

        if ($talentTypes->isEmpty()) {
            $this->error('No talent_types rows found. Run TalentTypeSeeder first: php artisan db:seed --class=TalentTypeSeeder');
            return self::FAILURE;
        }

        $users = User::with([
            'professionalProfile.category.parent',
            'professionalProfile.educationProfile',
            'professionalProfile.skilledLabourProfile',
            'talentTypes',
        ])->whereNotNull('onboarding_completed')
          ->where('onboarding_completed', true)
          ->get();

        $counts = ['skipped' => 0, 'professional' => 0, 'teacher' => 0, 'skilled_labour' => 0, 'errors' => 0];

        foreach ($users as $user) {
            $profile = $user->professionalProfile;

            // Customers / hirers: no professional profile → no talent classification
            if (!$profile) {
                $this->line("  [{$user->id}] {$user->name} — SKIP (no professional profile / customer only)");
                $counts['skipped']++;
                continue;
            }

            $classification = $this->inferClassification($profile);

            if ($classification === null) {
                $this->line("  [{$user->id}] {$user->name} — SKIP (could not infer classification from category: {$profile->category?->name})");
                $counts['skipped']++;
                continue;
            }

            $talentType = $talentTypes->get($classification->value);

            if (!$talentType) {
                $this->error("  [{$user->id}] {$user->name} — ERROR: TalentType '{$classification->value}' not found in DB");
                $counts['errors']++;
                continue;
            }

            $this->info("  [{$user->id}] {$user->name} → {$classification->label()} (category: {$profile->category?->name})");

            if (!$dryRun) {
                DB::transaction(function () use ($user, $talentType, $classification, $profile) {
                    // Insert talent_type_user row (idempotent)
                    $user->talentTypes()->syncWithoutDetaching([
                        $talentType->id => ['completed_at' => now()],
                    ]);

                    // For Skilled Labour: also ensure skilled_labour_profiles row exists
                    if ($classification === TalentClassification::SkilledLabour) {
                        SkilledLabourProfile::firstOrCreate(
                            ['professional_profile_id' => $profile->id],
                            [
                                'trade_category_id'    => $profile->category_id,
                                'is_certified'         => false,
                                'certification_notes'  => null,
                            ]
                        );
                    }

                    // Ensure talent_onboarding_started_at is set on the user
                    if (!$user->talent_onboarding_started_at) {
                        $user->update(['talent_onboarding_started_at' => now()]);
                    }
                });
            }

            $counts[$classification->value]++;
        }

        $this->newLine();
        $this->table(
            ['Classification', 'Count'],
            [
                ['Professional', $counts['professional']],
                ['Teacher',      $counts['teacher']],
                ['Skilled Labour', $counts['skilled_labour']],
                ['Skipped',      $counts['skipped']],
                ['Errors',       $counts['errors']],
            ]
        );

        if ($dryRun) {
            $this->warn('Dry run complete. No changes were written.');
        } else {
            $this->info('Backfill complete.');
        }

        return self::SUCCESS;
    }

    private function inferClassification(ProfessionalProfile $profile): ?TalentClassification
    {
        $category = $profile->category;
        if (!$category) {
            return null;
        }

        // Resolve the parent category slug for hierarchy-based matching
        $parentSlug = $category->parent_id
            ? ($category->parent?->slug ?? $category->slug)
            : $category->slug;

        // Education & Tutoring → Teacher
        if ($parentSlug === self::TEACHER_PARENT_SLUG || $category->slug === self::TEACHER_PARENT_SLUG) {
            return TalentClassification::Teacher;
        }

        // Home & Technical Services → Skilled Labour
        if ($parentSlug === self::SKILLED_LABOUR_PARENT_SLUG || $category->slug === self::SKILLED_LABOUR_PARENT_SLUG) {
            return TalentClassification::SkilledLabour;
        }

        // Creative, Business, Beauty → Professional
        if (in_array($parentSlug, self::PROFESSIONAL_PARENT_SLUGS, true) ||
            in_array($category->slug, self::PROFESSIONAL_PARENT_SLUGS, true)) {
            return TalentClassification::Professional;
        }

        return null;
    }
}
