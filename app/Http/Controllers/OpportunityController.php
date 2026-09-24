<?php

namespace App\Http\Controllers;

use App\Enums\OpportunityStatus;
use App\Enums\TeachingMode;
use App\Models\Category;
use App\Models\EducationOpportunityDetails;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    /**
     * Store a newly created opportunity in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'opportunity_type' => 'required|string|max:100',
            'budget' => 'nullable|string|max:100',
            'budget_min' => 'nullable|integer',
            'budget_max' => 'nullable|integer',
            'description' => 'required|string|min:10',
            
            // Tutoring specific optional fields
            'subject_id' => 'nullable|exists:subjects,id',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'teaching_mode' => 'nullable|string|in:physical,online,both',
            'schedule_notes' => 'nullable|string|max:255',
        ]);

        $category = Category::find($validated['category_id']);
        $isAcademic = $category && (str_contains(strtolower($category->name), 'education') || str_contains(strtolower($category->name), 'tutor'));

        // Parse numeric budgets
        $budgetMin = $validated['budget_min'] ?? null;
        $budgetMax = $validated['budget_max'] ?? null;

        if (!$budgetMin && !empty($validated['budget'])) {
            preg_match_all('/\d+/', str_replace(',', '', $validated['budget']), $matches);
            if (!empty($matches[0])) {
                $budgetMin = (int) $matches[0][0];
                if (isset($matches[0][1])) {
                    $budgetMax = (int) $matches[0][1];
                }
            }
        }

        $opportunity = Opportunity::create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'opportunity_type' => $validated['opportunity_type'],
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
            'status' => OpportunityStatus::Open,
        ]);

        // Save education details if academic category or subject passed
        if ($isAcademic || !empty($validated['subject_id'])) {
            EducationOpportunityDetails::create([
                'opportunity_id' => $opportunity->id,
                'subject_id' => $validated['subject_id'] ?? null,
                'education_level_id' => $validated['education_level_id'] ?? null,
                'teaching_mode' => !empty($validated['teaching_mode']) ? TeachingMode::from($validated['teaching_mode']) : TeachingMode::Both,
                'schedule_notes' => $validated['schedule_notes'] ?? null,
                'student_notes' => null,
            ]);
        }

        // Dispatch queued email notification job for matching professionals
        \App\Jobs\SendJobOpportunityAlertsJob::dispatch($opportunity);

        return redirect()->to(url('/dashboard#opportunities-section'))
            ->with('status', 'Your opportunity "' . $opportunity->title . '" has been published successfully!');
    }

    /**
     * Display the authenticated user's job postings
     */
    public function myJobs(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load(['professionalProfile']);

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

        $categories = Category::orderBy('name')->get();
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $levels = \App\Models\EducationLevel::all();

        $myOpportunities = Opportunity::with(['category', 'educationDetails.subject', 'educationDetails.educationLevel', 'connectionRequests'])
            ->withCount('connectionRequests')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $userNotifications = $user->notifications()->take(15)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard.my-jobs', compact(
            'user', 
            'completionPercentage', 
            'myOpportunities', 
            'categories', 
            'subjects', 
            'levels',
            'userNotifications',
            'unreadCount'
        ));
    }

    /**
     * Delete an opportunity posting created by the user
     */
    public function destroy(Request $request, $id)
    {
        $opportunity = Opportunity::findOrFail($id);

        if ($opportunity->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action. You can only delete opportunity postings you created.');
        }

        $title = $opportunity->title;
        $opportunity->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Opportunity posting deleted successfully.',
            ]);
        }

        return redirect()->to(url('/dashboard/my-jobs'))
            ->with('status', 'Opportunity posting "' . $title . '" has been deleted successfully.');
    }
}
