<?php

namespace App\Services;

use App\Models\Skill;
use App\Models\Subject;
use Illuminate\Support\Str;

class SkillAndSubjectResolver
{
    /**
     * Resolve array of skill inputs (IDs or custom text names) into an array of integer Skill IDs.
     * Automatically creates new Skill records for non-existent text inputs.
     *
     * @param  array|string|null $skills
     * @param  array|string|null $customSkills
     * @return int[]
     */
    public static function resolveSkillIds($skills = null, $customSkills = null): array
    {
        $skillIds = [];

        // 1. Process main skills input (can be array of IDs or names)
        if ($skills) {
            $items = is_array($skills) ? $skills : explode(',', (string) $skills);
            foreach ($items as $item) {
                $itemStr = trim((string) $item);
                if ($itemStr === '') {
                    continue;
                }

                if (is_numeric($itemStr)) {
                    $skillIds[] = (int) $itemStr;
                } else {
                    $slug = Str::slug($itemStr);
                    if ($slug !== '') {
                        $skill = Skill::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $itemStr]
                        );
                        $skillIds[] = (int) $skill->id;
                    }
                }
            }
        }

        // 2. Process custom_skills input (comma-separated string or array of custom names)
        if ($customSkills) {
            $customItems = is_array($customSkills) ? $customSkills : explode(',', (string) $customSkills);
            foreach ($customItems as $cItem) {
                $name = trim((string) $cItem);
                if ($name === '') {
                    continue;
                }

                $slug = Str::slug($name);
                if ($slug !== '') {
                    $skill = Skill::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $name]
                    );
                    $skillIds[] = (int) $skill->id;
                }
            }
        }

        return array_values(array_unique($skillIds));
    }

    /**
     * Resolve array of subject inputs (IDs or custom text names) into an array of integer Subject IDs.
     * Automatically creates new Subject records for non-existent text inputs.
     *
     * @param  array|string|null $subjects
     * @param  array|string|null $customSubjects
     * @return int[]
     */
    public static function resolveSubjectIds($subjects = null, $customSubjects = null): array
    {
        $subjectIds = [];

        // 1. Process main subjects input (can be array of IDs or names)
        if ($subjects) {
            $items = is_array($subjects) ? $subjects : explode(',', (string) $subjects);
            foreach ($items as $item) {
                $itemStr = trim((string) $item);
                if ($itemStr === '') {
                    continue;
                }

                if (is_numeric($itemStr)) {
                    $subjectIds[] = (int) $itemStr;
                } else {
                    $slug = Str::slug($itemStr);
                    if ($slug !== '') {
                        $subject = Subject::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $itemStr]
                        );
                        $subjectIds[] = (int) $subject->id;
                    }
                }
            }
        }

        // 2. Process custom_subjects input (comma-separated string or array of custom names)
        if ($customSubjects) {
            $customItems = is_array($customSubjects) ? $customSubjects : explode(',', (string) $customSubjects);
            foreach ($customItems as $cItem) {
                $name = trim((string) $cItem);
                if ($name === '') {
                    continue;
                }

                $slug = Str::slug($name);
                if ($slug !== '') {
                    $subject = Subject::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $name]
                    );
                    $subjectIds[] = (int) $subject->id;
                }
            }
        }

        return array_values(array_unique($subjectIds));
    }
}
