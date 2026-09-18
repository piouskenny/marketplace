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

        // Fetch categories, subjects, and education levels for modal forms and filtering
        $categories = Category::whereNull('parent_id')->get();
        $subjects = \App\Models\Subject::all();
        $levels = \App\Models\EducationLevel::all();

        // Fetch DB Opportunities
        $dbOpportunities = \App\Models\Opportunity::with(['category', 'educationDetails.subject', 'educationDetails.educationLevel', 'user'])
            ->where('status', \App\Enums\OpportunityStatus::Open)
            ->latest()
            ->get();

        $formattedDbOpportunities = [];
        foreach ($dbOpportunities as $dbOpp) {
            $catName = $dbOpp->category->name ?? 'General Opportunity';
            $isTutoring = str_contains(strtolower($catName), 'education') || str_contains(strtolower($catName), 'tutor');
            
            $budgetText = 'Negotiable';
            if ($dbOpp->budget_min && $dbOpp->budget_max) {
                $budgetText = '₦' . number_format($dbOpp->budget_min) . ' - ₦' . number_format($dbOpp->budget_max);
            } elseif ($dbOpp->budget_min) {
                $budgetText = '₦' . number_format($dbOpp->budget_min);
            }

            $formattedDbOpportunities[] = [
                'id' => $dbOpp->id,
                'user_id' => $dbOpp->user_id,
                'is_own' => $dbOpp->user_id === $user->id,
                'title' => $dbOpp->title,
                'category' => $isTutoring ? 'Academic Tutoring' : $catName,
                'category_icon' => $isTutoring ? '🎓' : '💼',
                'location' => $dbOpp->location,
                'type' => $dbOpp->opportunity_type,
                'budget' => $budgetText,
                'time_ago' => $dbOpp->created_at ? $dbOpp->created_at->diffForHumans() : 'Just now',
                'description' => $dbOpp->description,
                'tags' => array_filter([
                    $dbOpp->opportunity_type,
                    $dbOpp->location,
                    optional(optional($dbOpp->educationDetails)->subject)->name,
                    optional(optional($dbOpp->educationDetails)->educationLevel)->name,
                ]),
            ];
        }

        // Sample Opportunities list matching business logic
        $sampleOpportunities = [
            [
                'id' => 101,
                'user_id' => 999,
                'is_own' => false,
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
                'id' => 102,
                'user_id' => 998,
                'is_own' => false,
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
                'id' => 103,
                'user_id' => 997,
                'is_own' => false,
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
                'id' => 104,
                'user_id' => 996,
                'is_own' => false,
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
                'id' => 105,
                'user_id' => 995,
                'is_own' => false,
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

        $opportunities = array_merge($formattedDbOpportunities, $sampleOpportunities);

        // Curated Suggested Jobs matching JobTrack UI design
        $suggestedJobs = [
            [
                'id' => 1,
                'title' => 'SS2 Physics & Math Tutor',
                'client_name' => 'Ikeja Household',
                'category' => 'Academic Tutoring',
                'avatar' => asset('images/avatars/babajide.png'),
                'logo_bg' => 'bg-indigo-100 text-indigo-700',
                'time_ago' => '1 day ago',
                'applicants_count' => 26,
                'budget' => '₦15,000 / wk',
                'location' => 'Ikeja, Lagos',
            ],
            [
                'id' => 2,
                'title' => 'Certified Solar Installer',
                'client_name' => 'Lekki Residence',
                'category' => 'Electrical & Solar',
                'avatar' => asset('images/avatars/emeka.png'),
                'logo_bg' => 'bg-sky-100 text-sky-700',
                'time_ago' => '2 days ago',
                'applicants_count' => 18,
                'budget' => '₦45,000',
                'location' => 'Lekki Phase 1',
            ],
            [
                'id' => 3,
                'title' => 'Bespoke Senator Tailor',
                'client_name' => 'Surulere Studio',
                'category' => 'Fashion & Craft',
                'avatar' => asset('images/avatars/funmi.png'),
                'logo_bg' => 'bg-emerald-100 text-emerald-700',
                'time_ago' => '1 day ago',
                'applicants_count' => 34,
                'budget' => '₦30,000',
                'location' => 'Surulere, Lagos',
            ],
            [
                'id' => 4,
                'title' => 'WAEC English Instructor',
                'client_name' => 'Garki Education Center',
                'category' => 'Academic Tutoring',
                'avatar' => asset('images/avatars/zainab.png'),
                'logo_bg' => 'bg-purple-100 text-purple-700',
                'time_ago' => '3 days ago',
                'applicants_count' => 12,
                'budget' => '₦20,000 / wk',
                'location' => 'Garki, Abuja',
            ],
        ];

        $userNotifications = $user->notifications()->take(15)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard', compact('user', 'completionPercentage', 'categories', 'subjects', 'levels', 'opportunities', 'suggestedJobs', 'userNotifications', 'unreadCount'));
    }

    /**
     * Dashboard Find Talent Page
     */
    public function talent(Request $request, \App\Services\ProfessionalDiscoveryService $discoveryService)
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
        $selectedSubject = $request->input('subject_id') ? (int) $request->input('subject_id') : null;
        $selectedLevel = $request->input('education_level_id') ? (int) $request->input('education_level_id') : null;
        $selectedTeachingMode = trim($request->input('teaching_mode', 'All'));
        $selectedMinRating = $request->input('min_rating') ? (float) $request->input('min_rating') : 0;

        $categories = Category::whereNull('parent_id')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $educationLevels = \App\Models\EducationLevel::orderBy('id')->get();

        $filters = [
            'query' => $searchQuery,
            'category' => $selectedCategory,
            'location' => $selectedLocation,
            'subject_id' => $selectedSubject,
            'education_level_id' => $selectedLevel,
            'teaching_mode' => $selectedTeachingMode,
            'min_rating' => $selectedMinRating,
        ];

        $paginated = $discoveryService->search($filters, 50);
        $professionals = $paginated->items();

        $userNotifications = $user->notifications()->take(15)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard.talent', compact(
            'user',
            'completionPercentage',
            'professionals',
            'categories',
            'subjects',
            'educationLevels',
            'searchQuery',
            'selectedCategory',
            'selectedLocation',
            'selectedSubject',
            'selectedLevel',
            'selectedTeachingMode',
            'selectedMinRating',
            'userNotifications',
            'unreadCount'
        ));
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

        // Session Overrides for accepted, declined, and paid connections
        $sessionAccepted = session()->get('accepted_connections', []);
        $sessionDeclined = session()->get('declined_connections', []);
        $sessionPaid = session()->get('paid_connections', []);

        // Fetch all DB Connection Requests involving the current user (both incoming and outgoing)
        $dbConnections = \App\Models\ConnectionRequest::with([
            'initiator.professionalProfile.category',
            'initiator.professionalProfile.skills',
            'recipient.professionalProfile',
            'opportunity.category',
            'conversation.messages.sender',
        ])
        ->where(function ($q) use ($user) {
            $q->where('initiator_id', $user->id)
              ->orWhere('recipient_id', $user->id);
        })
        ->latest()
        ->get();

        $processedConnIds = [];
        $dynamicConversations = [];

        foreach ($dbConnections as $req) {
            if (in_array($req->id, $processedConnIds)) {
                continue;
            }
            $processedConnIds[] = $req->id;

            $isIncoming = (int) $req->recipient_id === (int) $user->id;
            $otherUser = $isIncoming ? $req->initiator : $req->recipient;

            $dbStatus = $req->status instanceof \App\Enums\ConnectionStatus 
                ? $req->status->value 
                : (string) $req->status;

            if ($dbStatus === \App\Enums\ConnectionStatus::Connected->value || $req->conversation) {
                $currentStatus = 'connected';
            } elseif ($dbStatus === \App\Enums\ConnectionStatus::Declined->value) {
                $currentStatus = 'declined';
            } elseif ($dbStatus === \App\Enums\ConnectionStatus::Accepted->value) {
                $currentStatus = in_array($req->id, $sessionPaid) ? 'connected' : 'accepted';
            } else {
                $currentStatus = 'pending';
                if (in_array($req->id, $sessionPaid)) {
                    $currentStatus = 'connected';
                } elseif (in_array($req->id, $sessionAccepted)) {
                    $currentStatus = 'accepted';
                } elseif (in_array($req->id, $sessionDeclined)) {
                    $currentStatus = 'declined';
                }
            }

            $dbConv = $req->conversation;

            $msgs = [];
            if ($dbConv && $dbConv->messages->isNotEmpty()) {
                foreach ($dbConv->messages as $m) {
                    $msgs[] = [
                        'id' => $m->id,
                        'sender' => (int) $m->sender_id === (int) $user->id ? 'me' : 'them',
                        'text' => $m->body,
                        'time' => $m->created_at ? $m->created_at->toIso8601String() : 'Just now',
                        'read_at' => $m->read_at ? $m->read_at->toIso8601String() : null,
                    ];
                }
            } else {
                $msgs = [
                    [
                        'id' => 1,
                        'sender' => $isIncoming ? 'them' : 'me',
                        'text' => $req->initial_message ?? ($isIncoming ? 'Hello! I am interested in your opportunity.' : 'Application & Connection Request Submitted.'),
                        'time' => $req->created_at ? $req->created_at->toIso8601String() : 'Just now',
                    ]
                ];
            }

            $profile = $isIncoming && $otherUser ? $otherUser->professionalProfile : null;
            $skillsList = $profile && $profile->skills ? $profile->skills->pluck('name')->toArray() : ['Academic Tutoring', 'Mathematics'];

            $lastTimestamp = $dbConv && $dbConv->last_message_at 
                ? $dbConv->last_message_at->timestamp 
                : ($req->created_at ? $req->created_at->timestamp : time());

            $dynamicConversations[] = [
                'id' => 'conn_' . $req->id,
                'connection_id' => $req->id,
                'db_conversation_id' => $dbConv ? $dbConv->id : null,
                'is_incoming' => $isIncoming,
                'name' => $otherUser ? $otherUser->name : ($isIncoming ? 'Applicant User' : 'Job Owner / Household'),
                'title' => $req->opportunity ? $req->opportunity->title : 'Opportunity Connection Request',
                'avatar' => asset('images/avatars/babajide.png'),
                'online' => true,
                'location' => $profile ? ($profile->location ?? 'Lagos, Nigeria') : ($req->opportunity ? $req->opportunity->location : 'Lagos, Nigeria'),
                'category' => $req->opportunity && $req->opportunity->category ? $req->opportunity->category->name : 'General Service',
                'unread' => $dbConv ? $dbConv->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count() : ($isIncoming ? 1 : 0),
                'last_time' => $dbConv && $dbConv->last_message_at ? $dbConv->last_message_at->diffForHumans() : ($req->created_at ? $req->created_at->diffForHumans() : 'Just now'),
                'updated_timestamp' => $lastTimestamp,
                'status' => $currentStatus,
                'applicant_profile' => $isIncoming ? [
                    'name' => $otherUser ? $otherUser->name : 'Applicant User',
                    'avatar' => asset('images/avatars/babajide.png'),
                    'title' => $profile->headline ?? 'Verified Skill Marketplace Talent',
                    'category' => $profile && $profile->category ? $profile->category->name : 'Academic Tutoring',
                    'location' => $profile->location ?? 'Lagos, Nigeria',
                    'phone' => $otherUser->phone ?? '+234 802 345 6789',
                    'email' => $otherUser->email ?? 'applicant@example.com',
                    'bio' => $profile->bio ?? 'Qualified professional offering expert tutoring and contract services.',
                    'skills' => !empty($skillsList) ? $skillsList : ['Tutoring', 'Mentorship', 'Subject Prep'],
                    'education' => 'Higher Degree — University of Lagos',
                    'rating' => '4.9 ★ (24 Reviews)',
                    'verified' => true,
                ] : null,
                'messages' => $msgs,
            ];
        }

        // Existing DB names and titles for deduplication
        $existingNames = collect($dynamicConversations)->pluck('name')->map(fn($n) => strtolower(trim($n)))->toArray();
        $existingTitles = collect($dynamicConversations)->pluck('title')->map(fn($t) => strtolower(trim($t)))->toArray();

        // Process pending application sessions if not already in DB
        $pendingApps = session()->get('pending_applications', []);
        foreach ($pendingApps as $app) {
            $connId = (int) $app['id'];
            $appNameLower = strtolower(trim($app['name'] ?? ''));
            $appTitleLower = strtolower(trim($app['title'] ?? ''));

            if (!in_array($connId, $processedConnIds) && !in_array($appNameLower, $existingNames) && !in_array($appTitleLower, $existingTitles)) {
                $processedConnIds[] = $connId;
                $currentStatus = 'pending';
                if (in_array($connId, $sessionPaid)) {
                    $currentStatus = 'connected';
                } elseif (in_array($connId, $sessionAccepted)) {
                    $currentStatus = 'accepted';
                } elseif (in_array($connId, $sessionDeclined)) {
                    $currentStatus = 'declined';
                }

                $dynamicConversations[] = [
                    'id' => 'conn_' . $connId,
                    'connection_id' => $connId,
                    'db_conversation_id' => null,
                    'is_incoming' => false,
                    'name' => $app['name'],
                    'title' => $app['title'],
                    'avatar' => $app['avatar'] ?? asset('images/avatars/babajide.png'),
                    'online' => false,
                    'location' => $app['location'] ?? 'Lagos, Nigeria',
                    'category' => $app['category'] ?? 'General Service',
                    'unread' => 0,
                    'last_time' => $app['last_time'] ?? 'Just now',
                    'updated_timestamp' => time(),
                    'status' => $currentStatus,
                    'messages' => [
                        [
                            'id' => 1,
                            'sender' => 'me',
                            'text' => 'Application & Connection Request Submitted: "' . ($app['note'] ?? 'I am interested in this opportunity.') . '"',
                            'time' => 'Just now'
                        ]
                    ]
                ];
            }
        }

        // Merge dynamic user conversations
        $conversations = $dynamicConversations;

        // Sort all conversations by updated_timestamp descending (latest at top)
        usort($conversations, function ($a, $b) {
            $tsA = $a['updated_timestamp'] ?? 0;
            $tsB = $b['updated_timestamp'] ?? 0;
            return $tsB <=> $tsA;
        });

        $userNotifications = $user->notifications()->take(15)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard.messages', compact('user', 'completionPercentage', 'conversations', 'userNotifications', 'unreadCount'));
    }
}
