<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\EducationProfile;
use App\Models\ProfessionalProfile;
use App\Models\Skill;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        $homeCategory = Category::where('slug', Str::slug('Home & Technical Services'))->first();
        $eduCategory = Category::where('slug', Str::slug('Education & Tutoring'))->first();
        $creativeCategory = Category::where('slug', Str::slug('Creative & Digital Services'))->first();
        $businessCategory = Category::where('slug', Str::slug('Business & Professional'))->first();

        $mathSubject = Subject::where('name', 'LIKE', '%Mathematics%')->first();
        $physicsSubject = Subject::where('name', 'LIKE', '%Physics%')->first();
        $englishSubject = Subject::where('name', 'LIKE', '%English%')->first();
        $frenchSubject = Subject::where('name', 'LIKE', '%French%')->first();
        $chemSubject = Subject::where('name', 'LIKE', '%Chemistry%')->first();

        $sssLevel = EducationLevel::where('name', 'LIKE', '%Senior Secondary%')->first();
        $jssLevel = EducationLevel::where('name', 'LIKE', '%Junior Secondary%')->first();

        $professionals = [
            [
                'name' => 'David Olanrewaju',
                'email' => 'david.olanrewaju@example.com',
                'phone' => '08031234567',
                'location' => 'Ikeja, Lagos',
                'display_name' => 'David O. — Senior WAEC & Mathematics Tutor',
                'bio' => 'Certified mathematics and physics tutor with 8+ years experience preparing SSS3 students for WAEC, NECO & JAMB. 98% pass rate.',
                'category_id' => $eduCategory?->id,
                'years_of_experience' => 8,
                'hourly_rate' => 5000,
                'rating' => 5.0,
                'reviews_count' => 34,
                'subjects' => [$mathSubject?->id, $physicsSubject?->id],
                'levels' => [$sssLevel?->id, $jssLevel?->id],
                'skills' => ['Mathematics', 'Physics', 'WAEC Prep', 'Algebra', 'Geometry'],
            ],
            [
                'name' => 'Milena Rodriguez',
                'email' => 'milena.rodriguez@example.com',
                'phone' => '08029876543',
                'location' => 'Lekki Phase 1, Lagos',
                'display_name' => 'Milena R. — Electrical & Solar Specialist',
                'bio' => 'Certified master electrician specializing in full house electrical rewiring, solar panel installation, circuit repairs, and industrial generator maintenance.',
                'category_id' => $homeCategory?->id,
                'years_of_experience' => 10,
                'hourly_rate' => 8500,
                'rating' => 4.9,
                'reviews_count' => 28,
                'skills' => ['Electrical Rewiring', 'Solar Installation', 'Circuit Maintenance', 'Generator Repairs'],
            ],
            [
                'name' => 'Emmanuel Chinedu',
                'email' => 'emmanuel.chinedu@example.com',
                'phone' => '08055554433',
                'location' => 'Yaba, Lagos',
                'display_name' => 'Emmanuel C. — Master Plumber & Pipe Fitter',
                'bio' => 'Professional plumbing and pipe fitting services including leak detection, bathroom fittings, water heater repairs, and borehole water pumping solutions.',
                'category_id' => $homeCategory?->id,
                'years_of_experience' => 7,
                'hourly_rate' => 4500,
                'rating' => 4.8,
                'reviews_count' => 19,
                'skills' => ['Plumbing', 'Leak Detection', 'Bathroom Fitting', 'Water Heater Repair'],
            ],
            [
                'name' => 'Amina Bello',
                'email' => 'amina.bello@example.com',
                'phone' => '08077778899',
                'location' => 'Garki, Abuja',
                'display_name' => 'Amina B. — French & English Language Specialist',
                'bio' => 'Bilingual educator providing conversational French lessons, English literature tutoring, essay writing prep, and adult literacy classes.',
                'category_id' => $eduCategory?->id,
                'years_of_experience' => 6,
                'hourly_rate' => 6000,
                'rating' => 4.9,
                'reviews_count' => 22,
                'subjects' => [$frenchSubject?->id, $englishSubject?->id],
                'levels' => [$sssLevel?->id, $jssLevel?->id],
                'skills' => ['French Language', 'English Literature', 'Essay Writing', 'Grammar'],
            ],
            [
                'name' => 'Blessing Okafor',
                'email' => 'blessing.okafor@example.com',
                'phone' => '08123456789',
                'location' => 'Victoria Island, Lagos',
                'display_name' => 'Blessing O. — Senior UI/UX & Web Developer',
                'bio' => 'Full-stack Laravel PHP developer & UI designer crafting modern mobile-responsive web applications, e-commerce stores, custom databases, and API integrations.',
                'category_id' => $creativeCategory?->id,
                'years_of_experience' => 5,
                'hourly_rate' => 12000,
                'rating' => 5.0,
                'reviews_count' => 41,
                'skills' => ['Laravel', 'PHP', 'TailwindCSS', 'Web Development', 'UI/UX Design', 'Vue.js'],
            ],
            [
                'name' => 'Sophia Bennett',
                'email' => 'sophia.bennett@example.com',
                'phone' => '08099887766',
                'location' => 'Port Harcourt, Rivers',
                'display_name' => 'Sophia B. — Professional Photographer & Videographer',
                'bio' => 'Creative portrait, wedding, and event photographer with top-tier lighting and 4K videography setup for corporate and family events.',
                'category_id' => $creativeCategory?->id,
                'years_of_experience' => 6,
                'hourly_rate' => 10000,
                'rating' => 4.9,
                'reviews_count' => 31,
                'skills' => ['Photography', 'Event Videography', 'Portrait Photography', 'Photo Editing'],
            ],
            [
                'name' => 'Marcus Adebayo',
                'email' => 'marcus.adebayo@example.com',
                'phone' => '08066554433',
                'location' => 'Central Business District, Abuja',
                'display_name' => 'Marcus A. — Tax & Financial Accounting Consultant',
                'bio' => 'Chartered accountant helping small businesses and corporate clients with QuickBooks bookkeeping, tax filing prep, auditing, and payroll management.',
                'category_id' => $businessCategory?->id,
                'years_of_experience' => 9,
                'hourly_rate' => 15000,
                'rating' => 5.0,
                'reviews_count' => 25,
                'skills' => ['Accounting', 'Tax Advisory', 'Bookkeeping', 'Financial Audit', 'QuickBooks'],
            ],
            [
                'name' => 'Grace Danjuma',
                'email' => 'grace.danjuma@example.com',
                'phone' => '08044332211',
                'location' => 'Enugu, Enugu State',
                'display_name' => 'Grace D. — Chemistry & Biology Science Tutor',
                'bio' => 'Experienced science tutor specializing in organic chemistry, cell biology, and laboratory practical prep for secondary school and pre-med students.',
                'category_id' => $eduCategory?->id,
                'years_of_experience' => 7,
                'hourly_rate' => 5500,
                'rating' => 4.9,
                'reviews_count' => 18,
                'subjects' => [$chemSubject?->id],
                'levels' => [$sssLevel?->id],
                'skills' => ['Chemistry', 'Biology', 'Science Practical Prep', 'WAEC Science'],
            ],
        ];

        foreach ($professionals as $pData) {
            $user = User::firstOrCreate(
                ['email' => $pData['email']],
                [
                    'name' => $pData['name'],
                    'phone' => $pData['phone'],
                    'location' => $pData['location'],
                    'onboarding_completed' => true,
                    'onboarding_intent' => 'offer_services',
                    'password' => Hash::make('password'),
                ]
            );

            $profile = ProfessionalProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id' => $pData['category_id'],
                    'display_name' => $pData['display_name'],
                    'bio' => $pData['bio'],
                    'location' => $pData['location'],
                    'years_of_experience' => $pData['years_of_experience'],
                    'phone' => $pData['phone'],
                    'contact_email' => $pData['email'],
                    'availability_status' => 'available',
                    'average_rating' => $pData['rating'],
                    'reviews_count' => $pData['reviews_count'],
                ]
            );

            // Sync skills
            $skillIds = [];
            foreach ($pData['skills'] as $skillName) {
                $skill = Skill::firstOrCreate(
                    ['slug' => Str::slug($skillName)],
                    ['name' => $skillName]
                );
                $skillIds[] = $skill->id;
            }
            $profile->skills()->sync($skillIds);

            // Sync education profile if subjects are present
            if (!empty($pData['subjects'])) {
                $eduProfile = EducationProfile::updateOrCreate(
                    ['professional_profile_id' => $profile->id],
                    ['teaching_mode' => 'both']
                );
                $eduProfile->subjects()->sync(array_filter($pData['subjects']));
                if (!empty($pData['levels'])) {
                    $eduProfile->educationLevels()->sync(array_filter($pData['levels']));
                }
            }
        }
    }
}
