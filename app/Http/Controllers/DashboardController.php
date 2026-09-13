<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Opportunity;
use App\Models\ProfessionalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard Main Home Overview Page
     */
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

    /**
     * Dashboard Find Talent Page
     */
    public function talent(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load([
            'professionalProfile.category',
            'professionalProfile.skills',
            'professionalProfile.educationProfile.subjects',
            'professionalProfile.educationProfile.educationLevels',
        ]);

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

        $searchQuery = trim($request->input('query', ''));
        $selectedCategory = trim($request->input('category', 'All'));
        $selectedLocation = trim($request->input('location', 'All'));

        $categories = Category::whereNull('parent_id')->get();

        $query = ProfessionalProfile::with([
            'user',
            'category',
            'skills',
            'educationProfile.subjects',
            'educationProfile.educationLevels',
        ]);

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('display_name', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('bio', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('location', 'LIKE', "%{$searchQuery}%")
                  ->orWhereHas('user', function ($uq) use ($searchQuery) {
                      $uq->where('name', 'LIKE', "%{$searchQuery}%")
                         ->orWhere('location', 'LIKE', "%{$searchQuery}%");
                  })
                  ->orWhereHas('category', function ($cq) use ($searchQuery) {
                      $cq->where('name', 'LIKE', "%{$searchQuery}%");
                  })
                  ->orWhereHas('skills', function ($sq) use ($searchQuery) {
                      $sq->where('name', 'LIKE', "%{$searchQuery}%");
                  })
                  ->orWhereHas('educationProfile.subjects', function ($subq) use ($searchQuery) {
                      $subq->where('name', 'LIKE', "%{$searchQuery}%");
                  });
            });
        }

        if (!empty($selectedCategory) && $selectedCategory !== 'All') {
            $query->where(function ($q) use ($selectedCategory) {
                $q->whereHas('category', function ($cq) use ($selectedCategory) {
                    $cq->where('name', 'LIKE', "%{$selectedCategory}%")
                       ->orWhere('slug', 'LIKE', "%{$selectedCategory}%");
                });
            });
        }

        if (!empty($selectedLocation) && $selectedLocation !== 'All') {
            $query->where('location', 'LIKE', "%{$selectedLocation}%");
        }

        $professionals = $query->orderBy('average_rating', 'desc')->get();

        return view('dashboard.talent', compact('user', 'completionPercentage', 'professionals', 'categories', 'searchQuery', 'selectedCategory', 'selectedLocation'));
    }

    /**
     * Dashboard Messages & Real-Time Chat Workspace
     */
    public function messages(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load([
            'professionalProfile.category',
            'professionalProfile.skills',
        ]);

        $completionPercentage = 35;
        if (!empty($user->phone)) {
            $completionPercentage += 15;
        }
        if (!empty($user->location)) {
            $completionPercentage += 15;
        }
        if ($user->professionalProfile) {
            $completionPercentage += 20;
        }
        if ($user->onboarding_completed) {
            $completionPercentage = 100;
        }

        // Rich Conversation Threads for testing & interactive demo
        $conversations = [
            [
                'id' => 101,
                'name' => 'Babajide Ogundele',
                'title' => 'SS2 Mathematics & Physics Tutor',
                'avatar' => asset('images/avatars/babajide.png'),
                'online' => true,
                'location' => 'Ikeja, Lagos',
                'category' => 'Academic Tutoring',
                'unread' => 2,
                'last_time' => '10:42 AM',
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'them',
                        'text' => 'Hello! Thanks for reaching out regarding the SS2 Mathematics and Physics tutoring position.',
                        'time' => '10:15 AM'
                    ],
                    [
                        'id' => 2,
                        'sender' => 'me',
                        'text' => 'Hi Babajide! Yes, we need someone who can prepare our student for WAEC exams starting next term.',
                        'time' => '10:20 AM'
                    ],
                    [
                        'id' => 3,
                        'sender' => 'them',
                        'text' => 'Perfect. I have 6+ years of WAEC prep experience with an 88% distinction rate. Are you available for a 3-day weekly schedule?',
                        'time' => '10:30 AM'
                    ],
                    [
                        'id' => 4,
                        'sender' => 'them',
                        'text' => 'I can start tomorrow morning at 10 AM if that suits your schedule!',
                        'time' => '10:42 AM'
                    ],
                ]
            ],
            [
                'id' => 102,
                'name' => 'Funmi Adebayo',
                'title' => 'Bespoke Fashion Designer & Tailor',
                'avatar' => asset('images/avatars/funmi.png'),
                'online' => true,
                'location' => 'Surulere, Lagos',
                'category' => 'Fashion & Craft',
                'unread' => 0,
                'last_time' => 'Yesterday',
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'them',
                        'text' => 'Good afternoon! Your custom Senator attire and Agbada measurements have been finalized.',
                        'time' => 'Yesterday 2:15 PM'
                    ],
                    [
                        'id' => 2,
                        'sender' => 'me',
                        'text' => 'Awesome Funmi! When will the fitting session be ready?',
                        'time' => 'Yesterday 2:45 PM'
                    ],
                    [
                        'id' => 3,
                        'sender' => 'them',
                        'text' => 'The initial fitting is ready for Friday afternoon at our Surulere studio.',
                        'time' => 'Yesterday 3:10 PM'
                    ]
                ]
            ],
            [
                'id' => 103,
                'name' => 'Emeka Okafor',
                'title' => 'Certified Electrician & Solar Installer',
                'avatar' => asset('images/avatars/emeka.png'),
                'online' => false,
                'location' => 'Lekki Phase 1, Lagos',
                'category' => 'Home & Technical',
                'unread' => 0,
                'last_time' => '2 days ago',
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'me',
                        'text' => 'Hello Engr. Emeka, do you handle inverter battery bank installation and 5kVA solar setups?',
                        'time' => '2 days ago'
                    ],
                    [
                        'id' => 2,
                        'sender' => 'them',
                        'text' => 'Yes I do! We provide full load audit, surge protection, and neat cable trunking.',
                        'time' => '2 days ago'
                    ]
                ]
            ],
            [
                'id' => 104,
                'name' => 'Zainab Ibrahim',
                'title' => 'WAEC / JAMB English Language Instructor',
                'avatar' => asset('images/avatars/zainab.png'),
                'online' => true,
                'location' => 'Maitama, Abuja',
                'category' => 'Academic Tutoring',
                'unread' => 0,
                'last_time' => '3 days ago',
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'them',
                        'text' => 'Hi! The comprehension and essay writing mock assessment results have been compiled.',
                        'time' => '3 days ago'
                    ],
                    [
                        'id' => 2,
                        'sender' => 'me',
                        'text' => 'Thank you Zainab. Looking forward to reviewing the score breakdown.',
                        'time' => '3 days ago'
                    ]
                ]
            ]
        ];

        return view('dashboard.messages', compact('user', 'completionPercentage', 'conversations'));
    }
}
