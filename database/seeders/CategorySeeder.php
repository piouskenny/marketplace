<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Subject;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Home & Technical Services',
                'description' => 'Skilled trades and home maintenance professionals',
                'icon' => 'wrench-screwdriver',
                'children' => [
                    'Electricians',
                    'Plumbers',
                    'Mechanics',
                    'AC & Refrigeration Technicians',
                    'Solar & Inverter Installers',
                    'Painters & Decorators',
                ],
            ],
            [
                'name' => 'Creative & Digital Services',
                'description' => 'Designers, developers, writers, and media creators',
                'icon' => 'computer-desktop',
                'children' => [
                    'Graphic Designers',
                    'Video Editors',
                    'Content Writers',
                    'Web & Software Developers',
                    'Photographers & Videographers',
                ],
            ],
            [
                'name' => 'Business & Professional',
                'description' => 'Business consulting, administration, and corporate services',
                'icon' => 'briefcase',
                'children' => [
                    'Bookkeepers & Accountants',
                    'Event Planners',
                    'Legal Advisors',
                    'Virtual Assistants',
                ],
            ],
            [
                'name' => 'Beauty & Lifestyle',
                'description' => 'Personal care, styling, and fitness professionals',
                'icon' => 'sparkles',
                'children' => [
                    'Makeup Artists',
                    'Hair Stylists',
                    'Tailors & Fashion Designers',
                    'Personal Trainers',
                ],
            ],
            [
                'name' => 'Education & Tutoring',
                'description' => 'Academic tutoring, language learning, and skill development',
                'icon' => 'academic-cap',
                'children' => [
                    'Academic Tutors',
                    'Language Instructors',
                    'Music Instructors',
                    'Exam Prep Specialists (WAEC/JAMB/IELTS)',
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'icon' => $catData['icon'],
                    'is_active' => true,
                ]
            );

            foreach ($catData['children'] as $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Seed default subjects for Education vertical
        $subjects = [
            'Mathematics',
            'English Language',
            'Physics',
            'Chemistry',
            'Biology',
            'Economics',
            'Computer Science & Coding',
            'Music & Instruments',
            'French',
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['slug' => Str::slug($subject)],
                ['name' => $subject]
            );
        }

        // Seed default education levels
        $levels = [
            'Early Childhood / Nursery',
            'Primary School',
            'Junior Secondary School (JSS)',
            'Senior Secondary School (SSS)',
            'Undergraduate / Tertiary',
            'Adult & Professional Education',
        ];

        foreach ($levels as $level) {
            EducationLevel::firstOrCreate(
                ['slug' => Str::slug($level)],
                ['name' => $level]
            );
        }
    }
}
