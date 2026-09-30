<?php

namespace Database\Seeders;

use App\Enums\TalentClassification;
use App\Models\TalentType;
use Illuminate\Database\Seeder;

class TalentTypeSeeder extends Seeder
{
    /**
     * Seed the three talent classification lookup rows.
     *
     * This seeder is idempotent — safe to run multiple times.
     * Rows are never deleted; only inserted if missing.
     */
    public function run(): void
    {
        $types = [
            [
                'slug'        => TalentClassification::Professional->value,
                'label'       => TalentClassification::Professional->label(),
                'description' => TalentClassification::Professional->description(),
                'icon'        => TalentClassification::Professional->icon(),
                'sort_order'  => 1,
            ],
            [
                'slug'        => TalentClassification::Teacher->value,
                'label'       => TalentClassification::Teacher->label(),
                'description' => TalentClassification::Teacher->description(),
                'icon'        => TalentClassification::Teacher->icon(),
                'sort_order'  => 2,
            ],
            [
                'slug'        => TalentClassification::SkilledLabour->value,
                'label'       => TalentClassification::SkilledLabour->label(),
                'description' => TalentClassification::SkilledLabour->description(),
                'icon'        => TalentClassification::SkilledLabour->icon(),
                'sort_order'  => 3,
            ],
        ];

        foreach ($types as $typeData) {
            TalentType::firstOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );
        }
    }
}
