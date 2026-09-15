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

        $userNotifications = $user->notifications()->take(15)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard.talent', compact('user', 'completionPercentage', 'professionals', 'categories', 'searchQuery', 'selectedCategory', 'selectedLocation', 'userNotifications', 'unreadCount'));
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

        // Pending Outgoing Application Connection Requests
        $pendingApps = session()->get('pending_applications', []);
        $dbOutgoingRequests = \App\Models\ConnectionRequest::with(['recipient', 'opportunity'])
            ->where('initiator_id', $user->id)
            ->latest()
            ->get();

        // DB Incoming Connection Requests for Job Owners
        $dbIncomingRequests = \App\Models\ConnectionRequest::with(['initiator.professionalProfile.category', 'initiator.professionalProfile.skills', 'opportunity'])
            ->where('recipient_id', $user->id)
            ->latest()
            ->get();

        $pendingConversations = [];

        // Outgoing Applications (Sent by me)
        foreach ($pendingApps as $app) {
            $connId = (int) $app['id'];
            $currentStatus = 'pending';
            if (in_array($connId, $sessionPaid)) {
                $currentStatus = 'connected';
            } elseif (in_array($connId, $sessionAccepted)) {
                $currentStatus = 'accepted';
            } elseif (in_array($connId, $sessionDeclined)) {
                $currentStatus = 'declined';
            }

            $pendingConversations[] = [
                'id' => 'conn_' . $app['id'],
                'connection_id' => $app['id'],
                'is_incoming' => false,
                'name' => $app['name'],
                'title' => $app['title'],
                'avatar' => $app['avatar'] ?? asset('images/avatars/babajide.png'),
                'online' => false,
                'location' => $app['location'] ?? 'Lagos, Nigeria',
                'category' => $app['category'] ?? 'General Service',
                'unread' => 0,
                'last_time' => $app['last_time'] ?? 'Just now',
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

        foreach ($dbOutgoingRequests as $req) {
            $alreadyInSession = collect($pendingConversations)->contains(function ($item) use ($req) {
                return isset($item['connection_id']) && $item['connection_id'] == $req->id;
            });
            if (!$alreadyInSession) {
                $currentStatus = $req->status->value;
                if (in_array($req->id, $sessionPaid)) {
                    $currentStatus = 'connected';
                } elseif (in_array($req->id, $sessionAccepted)) {
                    $currentStatus = 'accepted';
                } elseif (in_array($req->id, $sessionDeclined)) {
                    $currentStatus = 'declined';
                }

                $pendingConversations[] = [
                    'id' => 'conn_' . $req->id,
                    'connection_id' => $req->id,
                    'is_incoming' => false,
                    'name' => $req->recipient ? $req->recipient->name : 'Job Owner / Household',
                    'title' => $req->opportunity ? $req->opportunity->title : 'Opportunity Connection Request',
                    'avatar' => asset('images/avatars/babajide.png'),
                    'online' => false,
                    'location' => $req->opportunity ? $req->opportunity->location : 'Lagos, Nigeria',
                    'category' => $req->opportunity && $req->opportunity->category ? $req->opportunity->category->name : 'Service Request',
                    'unread' => 0,
                    'last_time' => $req->created_at ? $req->created_at->diffForHumans() : 'Just now',
                    'status' => $currentStatus,
                    'messages' => [
                        [
                            'id' => 1,
                            'sender' => 'me',
                            'text' => 'Application & Connection Request Submitted: "' . ($req->initial_message ?? 'I am interested in this opportunity.') . '"',
                            'time' => $req->created_at ? $req->created_at->format('g:i A') : 'Just now'
                        ]
                    ]
                ];
            }
        }

        // Incoming Connection Requests (Sent to me by Job Applicants)
        $incomingConversations = [];
        foreach ($dbIncomingRequests as $inc) {
            $currentStatus = $inc->status->value;
            if (in_array($inc->id, $sessionPaid)) {
                $currentStatus = 'connected';
            } elseif (in_array($inc->id, $sessionAccepted)) {
                $currentStatus = 'accepted';
            } elseif (in_array($inc->id, $sessionDeclined)) {
                $currentStatus = 'declined';
            }

            $applicantUser = $inc->initiator;
            $profile = $applicantUser ? $applicantUser->professionalProfile : null;
            $skillsList = $profile ? $profile->skills->pluck('name')->toArray() : ['Academic Tutoring', 'Mathematics'];

            $incomingConversations[] = [
                'id' => 'conn_' . $inc->id,
                'connection_id' => $inc->id,
                'is_incoming' => true,
                'name' => $applicantUser ? $applicantUser->name : 'Applicant User',
                'title' => $inc->opportunity ? $inc->opportunity->title : 'Opportunity Application',
                'avatar' => asset('images/avatars/babajide.png'),
                'online' => true,
                'location' => $profile->location ?? 'Lagos, Nigeria',
                'category' => $inc->opportunity && $inc->opportunity->category ? $inc->opportunity->category->name : 'Tutoring & Education',
                'unread' => 1,
                'last_time' => $inc->created_at ? $inc->created_at->diffForHumans() : 'Just now',
                'status' => $currentStatus,
                'applicant_profile' => [
                    'name' => $applicantUser ? $applicantUser->name : 'Applicant User',
                    'avatar' => asset('images/avatars/babajide.png'),
                    'title' => $profile->headline ?? 'Verified Skill Marketplace Talent',
                    'category' => $profile && $profile->category ? $profile->category->name : 'Academic Tutoring',
                    'location' => $profile->location ?? 'Lagos, Nigeria',
                    'phone' => $applicantUser->phone ?? '+234 802 345 6789',
                    'email' => $applicantUser->email ?? 'applicant@example.com',
                    'bio' => $profile->bio ?? 'Qualified professional offering expert tutoring and contract services.',
                    'skills' => !empty($skillsList) ? $skillsList : ['Tutoring', 'Mentorship', 'Subject Prep'],
                    'education' => 'Higher Degree — University of Lagos',
                    'rating' => '4.9 ★ (24 Reviews)',
                    'verified' => true,
                ],
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'them',
                        'text' => $inc->initial_message ?? 'Hello! I am highly interested in your opportunity and would love to connect and discuss details.',
                        'time' => $inc->created_at ? $inc->created_at->format('g:i A') : 'Just now'
                    ]
                ]
            ];
        }

        // Add a curated Demo Incoming Application for instant testing if no DB incoming request exists
        if (empty($incomingConversations)) {
            $demoConnId = 901;
            $demoStatus = 'pending';
            if (in_array($demoConnId, $sessionPaid)) {
                $demoStatus = 'connected';
            } elseif (in_array($demoConnId, $sessionAccepted)) {
                $demoStatus = 'accepted';
            } elseif (in_array($demoConnId, $sessionDeclined)) {
                $demoStatus = 'declined';
            }

            $incomingConversations[] = [
                'id' => 'conn_' . $demoConnId,
                'connection_id' => $demoConnId,
                'is_incoming' => true,
                'name' => 'Chinedu Eze',
                'title' => 'SS2 Mathematics & Physics Tutor Posting Application',
                'avatar' => asset('images/avatars/babajide.png'),
                'online' => true,
                'location' => 'Ikeja, Lagos',
                'category' => 'Academic Tutoring',
                'unread' => 1,
                'last_time' => '10 mins ago',
                'status' => $demoStatus,
                'applicant_profile' => [
                    'name' => 'Chinedu Eze',
                    'avatar' => asset('images/avatars/babajide.png'),
                    'title' => 'Senior Mathematics & Physics Tutor (WAEC Specialist)',
                    'category' => 'Academic Tutoring',
                    'location' => 'Ikeja, Lagos',
                    'phone' => '+234 803 456 7890',
                    'email' => 'chinedu.eze@example.com',
                    'bio' => 'Passionate STEM educator with over 6 years experience preparing SSS2 & SSS3 students for WAEC, NECO, and JAMB exams. 88% distinction rate.',
                    'skills' => ['Mathematics', 'Physics', 'Further Math', 'WAEC Prep', 'Exam Strategy'],
                    'education' => 'B.Sc. Industrial Physics (First Class) — University of Lagos',
                    'rating' => '4.9 ★ (32 Reviews)',
                    'verified' => true,
                ],
                'messages' => [
                    [
                        'id' => 1,
                        'sender' => 'them',
                        'text' => 'Good day! I saw your opportunity posting for SS2 Mathematics & Physics Tutoring. I have extensive WAEC prep experience and would love to assist your student.',
                        'time' => '10:30 AM'
                    ]
                ]
            ];
        }

        // DB Connected Conversations
        $dbConnectedConversations = \App\Models\Conversation::with(['connectionRequest.initiator', 'connectionRequest.recipient', 'connectionRequest.opportunity.category', 'messages.sender'])
            ->whereHas('connectionRequest', function ($q) use ($user) {
                $q->where('status', \App\Enums\ConnectionStatus::Connected)
                  ->where(function ($sub) use ($user) {
                      $sub->where('initiator_id', $user->id)
                          ->orWhere('recipient_id', $user->id);
                  });
            })
            ->orderBy('last_message_at', 'desc')
            ->get();

        $connectedConversations = [];
        foreach ($dbConnectedConversations as $conv) {
            $otherUser = $conv->connectionRequest->initiator_id === $user->id 
                ? $conv->connectionRequest->recipient 
                : $conv->connectionRequest->initiator;

            $msgs = [];
            foreach ($conv->messages as $m) {
                $msgs[] = [
                    'id' => $m->id,
                    'sender' => $m->sender_id === $user->id ? 'me' : 'them',
                    'text' => $m->body,
                    'time' => $m->created_at ? $m->created_at->format('g:i A') : 'Just now',
                    'read_at' => $m->read_at,
                ];
            }

            $connectedConversations[] = [
                'id' => 'db_conv_' . $conv->id,
                'db_conversation_id' => $conv->id,
                'connection_id' => $conv->connection_request_id,
                'name' => $otherUser ? $otherUser->name : 'Connected Partner',
                'title' => $conv->connectionRequest->opportunity ? $conv->connectionRequest->opportunity->title : 'Connected Opportunity',
                'avatar' => asset('images/avatars/babajide.png'),
                'online' => true,
                'location' => $otherUser->location ?? 'Lagos, Nigeria',
                'category' => $conv->connectionRequest->opportunity && $conv->connectionRequest->opportunity->category ? $conv->connectionRequest->opportunity->category->name : 'Direct Connection',
                'unread' => $conv->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count(),
                'last_time' => $conv->last_message_at ? $conv->last_message_at->diffForHumans() : ($conv->created_at ? $conv->created_at->diffForHumans() : 'Just now'),
                'status' => 'connected',
                'messages' => $msgs,
            ];
        }

        // Merge conversations
        $conversations = array_merge($connectedConversations, $incomingConversations, $pendingConversations, [
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
                'status' => 'connected',
                'is_incoming' => false,
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
                'status' => 'connected',
                'is_incoming' => false,
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
                'status' => 'connected',
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
                'status' => 'connected',
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
        ]);

        $userNotifications = $user->notifications()->take(15)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard.messages', compact('user', 'completionPercentage', 'conversations', 'userNotifications', 'unreadCount'));
    }
}
