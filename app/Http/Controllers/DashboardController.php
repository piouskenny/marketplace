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

        // Auto-seed real opportunities if database has fewer than 5 open opportunities
        $allDbOppsCount = \App\Models\Opportunity::where('status', \App\Enums\OpportunityStatus::Open)->count();
        if ($allDbOppsCount < 5) {
            $client1 = \App\Models\User::firstOrCreate(['email' => 'client.ikeja@example.com'], [
                'name' => 'Dr. Mrs. Adebayo (Ikeja Household)',
                'location' => 'Ikeja, Lagos',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'onboarding_completed' => true,
            ]);
            $client2 = \App\Models\User::firstOrCreate(['email' => 'client.lekki@example.com'], [
                'name' => 'Chief Emeka Okonkwo (Lekki Residence)',
                'location' => 'Lekki Phase 1, Lagos',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'onboarding_completed' => true,
            ]);
            $client3 = \App\Models\User::firstOrCreate(['email' => 'client.abuja@example.com'], [
                'name' => 'Alhaji Garki Household',
                'location' => 'Garki, Abuja',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'onboarding_completed' => true,
            ]);
            $client4 = \App\Models\User::firstOrCreate(['email' => 'client.yaba@example.com'], [
                'name' => 'TechVentures Studio (Yaba)',
                'location' => 'Yaba, Lagos',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'onboarding_completed' => true,
            ]);

            $eduCat = \App\Models\Category::where('slug', 'LIKE', '%education%')->first() ?? Category::first();
            $homeCat = \App\Models\Category::where('slug', 'LIKE', '%home%')->first() ?? Category::first();
            $creativeCat = \App\Models\Category::where('slug', 'LIKE', '%creative%')->first() ?? Category::first();

            $initialOpps = [
                [
                    'user_id' => $client1->id,
                    'category_id' => $eduCat?->id,
                    'title' => 'SS2 Mathematics & Physics Home Tutor Needed',
                    'description' => 'Looking for an experienced WAEC tutor for an SS2 student in Mathematics and Physics. 3 days a week in Ikeja.',
                    'location' => 'Ikeja, Lagos',
                    'opportunity_type' => 'Physical In-Person',
                    'budget_min' => 15000,
                    'budget_max' => 20000,
                    'status' => \App\Enums\OpportunityStatus::Open,
                ],
                [
                    'user_id' => $client2->id,
                    'category_id' => $homeCat?->id,
                    'title' => 'Experienced Electrician for Full House Rewiring & Solar',
                    'description' => 'Need a certified electrician to complete circuit repairs, inverter setup, and rewiring for a 4-bedroom duplex.',
                    'location' => 'Lekki Phase 1, Lagos',
                    'opportunity_type' => 'One-Off Contract',
                    'budget_min' => 45000,
                    'budget_max' => 60000,
                    'status' => \App\Enums\OpportunityStatus::Open,
                ],
                [
                    'user_id' => $client3->id,
                    'category_id' => $eduCat?->id,
                    'title' => 'WAEC English Language & Essay Private Tutor',
                    'description' => 'Seeking an energetic English Language tutor for oral English, essay writing, and comprehension prep for an SSS3 student.',
                    'location' => 'Garki, Abuja',
                    'opportunity_type' => 'Online or Physical',
                    'budget_min' => 20000,
                    'budget_max' => 25000,
                    'status' => \App\Enums\OpportunityStatus::Open,
                ],
                [
                    'user_id' => $client4->id,
                    'category_id' => $creativeCat?->id,
                    'title' => 'Senior Laravel & UI/UX Developer for E-Commerce App',
                    'description' => 'Looking for a Laravel web developer with strong TailwindCSS skills to build custom user dashboards and payment flow.',
                    'location' => 'Remote / Online',
                    'opportunity_type' => 'Freelance Contract',
                    'budget_min' => 85000,
                    'budget_max' => 120000,
                    'status' => \App\Enums\OpportunityStatus::Open,
                ],
                [
                    'user_id' => $client4->id,
                    'category_id' => $homeCat?->id,
                    'title' => 'AC Repair & Refrigerant Refill Technician (3 Split Units)',
                    'description' => 'Require an AC technician for routine servicing, filter cleaning, and refrigerant top-up for 3 split-unit air conditioners.',
                    'location' => 'Yaba, Lagos',
                    'opportunity_type' => 'Service Task',
                    'budget_min' => 25000,
                    'budget_max' => 30000,
                    'status' => \App\Enums\OpportunityStatus::Open,
                ],
            ];

            foreach ($initialOpps as $oppData) {
                if (!empty($oppData['category_id'])) {
                    \App\Models\Opportunity::firstOrCreate(
                        ['title' => $oppData['title'], 'user_id' => $oppData['user_id']],
                        $oppData
                    );
                }
            }
        }

        // Extract user profile details for skill & location recommendation scoring
        $userCategory = $user->professionalProfile?->category;
        $userCategoryId = $userCategory?->id;
        $userCategoryName = strtolower($userCategory?->name ?? $user->onboarding_intent ?? '');

        $userSkills = $user->professionalProfile?->skills 
            ? $user->professionalProfile->skills->pluck('name')->map(fn($s) => strtolower(trim($s)))->toArray() 
            : [];

        $userSubjects = $user->professionalProfile?->educationProfile?->subjects 
            ? $user->professionalProfile->educationProfile->subjects->pluck('name')->map(fn($s) => strtolower(trim($s)))->toArray() 
            : [];

        $userLocation = strtolower(trim($user->location ?? $user->professionalProfile?->location ?? ''));

        // Fetch all real DB Opportunities
        $allDbOpportunities = \App\Models\Opportunity::with([
            'category', 
            'educationDetails.subject', 
            'educationDetails.educationLevel', 
            'user', 
            'connectionRequests'
        ])
        ->where('status', \App\Enums\OpportunityStatus::Open)
        ->latest()
        ->get();

        $formattedDbOpportunities = [];
        $scoredSuggestedJobs = [];

        foreach ($allDbOpportunities as $dbOpp) {
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
                'is_own' => (int)$dbOpp->user_id === (int)$user->id,
                'title' => $dbOpp->title,
                'category' => $isTutoring ? 'Academic Tutoring' : $catName,
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

            // Calculate suggestion match score for jobs posted by other users
            if ((int)$dbOpp->user_id !== (int)$user->id) {
                $score = 0;
                $reasons = [];

                $oppTitleLower = strtolower($dbOpp->title);
                $oppDescLower = strtolower($dbOpp->description);
                $oppLocLower = strtolower($dbOpp->location);
                $oppCatLower = strtolower($catName);
                $subjectNameLower = strtolower(optional(optional($dbOpp->educationDetails)->subject)->name ?? '');

                // 1. Category match
                if ($userCategoryId && (int)$dbOpp->category_id === (int)$userCategoryId) {
                    $score += 35;
                    $reasons[] = 'Matching Category';
                } elseif (!empty($userCategoryName) && (str_contains($oppCatLower, $userCategoryName) || str_contains($userCategoryName, $oppCatLower))) {
                    $score += 25;
                    $reasons[] = 'Matching Category';
                }

                // 2. Location match
                if (!empty($userLocation) && (str_contains($oppLocLower, $userLocation) || str_contains($userLocation, $oppLocLower))) {
                    $score += 25;
                    $reasons[] = 'Matching Location';
                } elseif (str_contains(strtolower($dbOpp->opportunity_type), 'online') || str_contains($oppLocLower, 'remote') || str_contains($oppLocLower, 'online')) {
                    $score += 15;
                    $reasons[] = 'Remote Opportunity';
                }

                // 3. Skill & Subject match
                $allUserKeywords = array_merge($userSkills, $userSubjects);
                foreach ($allUserKeywords as $kw) {
                    if (!empty($kw) && (str_contains($oppTitleLower, $kw) || str_contains($oppDescLower, $kw) || str_contains($subjectNameLower, $kw))) {
                        $score += 20;
                        $reasons[] = 'Skill Match';
                        break;
                    }
                }

                $applicantCount = $dbOpp->connectionRequests ? $dbOpp->connectionRequests->count() : 0;

                $scoredSuggestedJobs[] = [
                    'id' => $dbOpp->id,
                    'user_id' => $dbOpp->user_id,
                    'title' => $dbOpp->title,
                    'client_name' => $dbOpp->user ? $dbOpp->user->name : 'Household Client',
                    'avatar' => $dbOpp->user ? $dbOpp->user->avatar_url : null,
                    'user_initial' => strtoupper(substr($dbOpp->user ? $dbOpp->user->name : 'C', 0, 1)),
                    'category' => $isTutoring ? 'Academic Tutoring' : $catName,
                    'budget' => $budgetText,
                    'location' => $dbOpp->location,
                    'time_ago' => $dbOpp->created_at ? $dbOpp->created_at->diffForHumans() : 'Just now',
                    'applicants_count' => $applicantCount,
                    'score' => $score,
                    'match_reason' => !empty($reasons) ? implode(' • ', array_unique($reasons)) : 'Recommended Opportunity',
                    'raw_opp' => [
                        'id' => $dbOpp->id,
                        'user_id' => $dbOpp->user_id,
                        'is_own' => false,
                        'title' => $dbOpp->title,
                        'category' => $isTutoring ? 'Academic Tutoring' : $catName,
                        'location' => $dbOpp->location,
                        'type' => $dbOpp->opportunity_type,
                        'budget' => $budgetText,
                        'time_ago' => $dbOpp->created_at ? $dbOpp->created_at->diffForHumans() : 'Just now',
                        'description' => $dbOpp->description,
                        'tags' => array_filter([
                            $dbOpp->opportunity_type,
                            $dbOpp->location,
                        ]),
                    ],
                ];
            }
        }

        // Sort suggested jobs by match score descending, then by id descending
        usort($scoredSuggestedJobs, function ($a, $b) {
            if ($a['score'] === $b['score']) {
                return $b['id'] <=> $a['id'];
            }
            return $b['score'] <=> $a['score'];
        });

        $suggestedJobs = array_slice($scoredSuggestedJobs, 0, 5);
        $opportunities = $formattedDbOpportunities;

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

            $calcUnread = 0;
            if ($dbConv && $dbConv->messages->isNotEmpty()) {
                $calcUnread = $dbConv->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count();
            } else {
                if ($isIncoming && is_null($req->read_at)) {
                    $calcUnread = 1;
                }
            }

            $dynamicConversations[] = [
                'id' => 'conn_' . $req->id,
                'connection_id' => $req->id,
                'db_conversation_id' => $dbConv ? $dbConv->id : null,
                'is_incoming' => $isIncoming,
                'other_user_id' => $otherUser ? $otherUser->id : null,
                'name' => $otherUser ? $otherUser->name : ($isIncoming ? 'Applicant User' : 'Job Owner / Household'),

                'title' => $req->opportunity ? $req->opportunity->title : 'Opportunity Connection Request',
                'avatar' => asset('images/avatars/babajide.png'),
                'online' => true,
                'location' => $profile ? ($profile->location ?? 'Lagos, Nigeria') : ($req->opportunity ? $req->opportunity->location : 'Lagos, Nigeria'),
                'category' => $req->opportunity && $req->opportunity->category ? $req->opportunity->category->name : 'General Service',
                'unread' => $calcUnread,
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
