<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load([
            'professionalProfile.category',
            'professionalProfile.skills',
            'professionalProfile.educationProfile.subjects',
            'professionalProfile.educationProfile.educationLevels',
        ]);

        // Calculate dynamic completion percentage
        $completionPercentage = 35;
        if (!empty($user->phone)) {
            $completionPercentage += 15;
        }
        if (!empty($user->location)) {
            $completionPercentage += 15;
        }
        if ($user->professionalProfile) {
            $completionPercentage += 20;
            if ($user->professionalProfile->educationProfile || $user->professionalProfile->skills->isNotEmpty()) {
                $completionPercentage += 15;
            }
        }
        if ($user->onboarding_completed) {
            $completionPercentage = 100;
        }

        // Fetch categories for filtering
        $categories = Category::whereNull('parent_id')->get();

        // Sample Opportunities list matching business logic
        $opportunities = [
            [
                'id' => 1,
                'title' => 'SS2 Mathematics & Physics Home Tutor Needed',
                'category' => 'Academic Tutoring',
                'category_icon' => '🎓',
                'location' => 'Ikeja, Lagos',
                'type' => 'Physical In-Person',
                'budget' => '₦15,000 / week',
                'time_ago' => '2 hours ago',
                'description' => 'Looking for a qualified tutor for an SS2 student preparing for WAEC exams in Mathematics and Physics. 3 days a week.',
                'tags' => ['Mathematics', 'Physics', 'WAEC Prep', 'SS2 Level'],
            ],
            [
                'id' => 2,
                'title' => 'Experienced Electrician for Full House Rewiring',
                'category' => 'Home & Technical',
                'category_icon' => '🔧',
                'location' => 'Lekki Phase 1, Lagos',
                'type' => 'One-Off Contract',
                'budget' => '₦45,000',
                'time_ago' => '4 hours ago',
                'description' => 'Need a certified electrician to complete circuit repairs and rewiring for a 3-bedroom duplex. Materials provided.',
                'tags' => ['Electrical Rewiring', 'Circuit Maintenance', 'Safety Certified'],
            ],
            [
                'id' => 3,
                'title' => 'WAEC English Language & Essay Private Tutor',
                'category' => 'Academic Tutoring',
                'category_icon' => '🎓',
                'location' => 'Garki, Abuja',
                'type' => 'Online or Physical',
                'budget' => '₦20,000 / week',
                'time_ago' => '1 day ago',
                'description' => 'Seeking an energetic English Language tutor for oral English, essay writing, and comprehension prep for an SSS3 student.',
                'tags' => ['English Language', 'Essay Writing', 'JAMB / WAEC'],
            ],
            [
                'id' => 4,
                'title' => 'AC Technician for Servicing & Gas Refill (3 Units)',
                'category' => 'Home & Technical',
                'category_icon' => '🔧',
                'location' => 'Yaba, Lagos',
                'type' => 'Service Task',
                'budget' => '₦25,000',
                'time_ago' => '1 day ago',
                'description' => 'Require an AC repair technician for routine servicing, filter cleaning, and refrigerant top-up for 3 split-unit air conditioners.',
                'tags' => ['AC Servicing', 'Refrigerant Refill', 'Maintenance'],
            ],
            [
                'id' => 5,
                'title' => 'Web Developer Needed for E-Commerce Marketplace',
                'category' => 'Creative & Digital',
                'category_icon' => '💻',
                'location' => 'Remote / Online',
                'type' => 'Freelance Gig',
                'budget' => '₦85,000',
                'time_ago' => '2 days ago',
                'description' => 'Looking for a Laravel web developer to integrate custom payment gateways and optimize user dashboard views.',
                'tags' => ['Laravel', 'PHP', 'TailwindCSS', 'Web Development'],
            ],
        ];

        return view('dashboard', compact('user', 'completionPercentage', 'categories', 'opportunities'));
    }
}


