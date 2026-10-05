<?php

namespace App\Http\Controllers;

use App\Actions\Profile\UpdateCustomerProfileAction;
use App\Actions\Profile\UpdateEducationProfileAction;
use App\Actions\Profile\UpdateProfessionalProfileAction;
use App\Actions\Profile\UpdateSkilledLabourProfileAction;
use App\Enums\TalentClassification;
use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Skill;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    // ─── Step 1: Intent Selection & Resumption ─────────────────────────────────

    /**
     * Step 1: Account goal selection page.
     * Offers: Hire / Offer Services / Skip for now.
     * If user already has incomplete classifications, resumes at the next incomplete step.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // If user has started onboarding and has incomplete classifications, resume onboarding
        if ($user->talentTypes()->whereNull('talent_type_user.completed_at')->exists()) {
            return $this->resumeOnboarding($user);
        }

        return view('onboarding.index', compact('user'));
    }

    /**
     * Resume interrupted onboarding at the next incomplete stage.
     */
    public function resumeOnboarding(User $user)
    {
        // 0. If user has no talent classifications selected yet, let them select classifications
        if ($user->talentTypes()->count() === 0) {
            return redirect()->route('onboarding.classification');
        }

        // 1. If no base ProfessionalProfile exists yet, user must fill shared base profile
        if (!$user->professionalProfile()->exists()) {
            return redirect()->route('onboarding.professional')
                ->with('status', 'Please complete your base profile details.');
        }

        // 2. If teacher classification is pending (incomplete)
        $teacherIncomplete = $user->talentTypes()->where('slug', 'teacher')->whereNull('talent_type_user.completed_at')->exists();
        if ($teacherIncomplete) {
            return redirect()->route('onboarding.tutor')
                ->with('status', 'Profile updated successfully. Please complete your teacher & tutoring details.');
        }

        // 3. If skilled_labour classification is pending (incomplete)
        $skilledIncomplete = $user->talentTypes()->where('slug', 'skilled_labour')->whereNull('talent_type_user.completed_at')->exists();
        if ($skilledIncomplete) {
            return redirect()->route('onboarding.skilled-labour')
                ->with('status', 'Profile updated successfully. Please complete your skilled trade details.');
        }

        // 4. All selected classifications complete
        $user->update(['onboarding_completed' => true]);

        return redirect()->to('/dashboard')
            ->with('status', 'Profile updated successfully! Welcome to Skill Link NG.');
    }

    // ─── Step 2A: Customer / Hirer ────────────────────────────────────────────

    /**
     * Step 2A: Customer / hirer setup page.
     */
    public function showClientForm(Request $request)
    {
        $user = $request->user();
        return view('onboarding.client', compact('user'));
    }

    /**
     * Submit customer / hirer profile.
     */
    public function submitClientForm(Request $request, UpdateCustomerProfileAction $action)
    {
        $validated = $request->validate([
            'phone'    => 'nullable|string|max:30',
            'location' => 'required|string|max:100',
        ]);

        $action->execute($request->user(), $validated);

        return redirect()->to('/dashboard')
            ->with('status', 'Account setup complete! You can now explore professionals or post opportunities.');
    }

    // ─── Step 2B: Classification Picker (talent providers) ───────────────────

    /**
     * Step 2B: Multi-select talent classification picker.
     * Shown when the user chooses "I offer services".
     */
    public function showClassificationForm(Request $request)
    {
        $user = $request->user();
        $classifications = TalentClassification::cases();
        return view('onboarding.classification', compact('user', 'classifications'));
    }

    /**
     * Submit classification selection.
     * Stores selected classifications (incomplete state) then redirects
     * to the first relevant sub-form.
     */
    public function submitClassificationForm(Request $request, \App\Actions\Profile\SetUserTalentTypeAction $setTalentType)
    {
        $validated = $request->validate([
            'classifications'   => 'required|array|min:1',
            'classifications.*' => 'in:' . implode(',', TalentClassification::slugs()),
        ]);

        $user = $request->user();
        $user->update([
            'talent_onboarding_started_at' => $user->talent_onboarding_started_at ?? now(),
            'onboarding_completed'         => false,
        ]);

        // Mark each chosen classification as started (completed_at = null)
        foreach ($validated['classifications'] as $slug) {
            $classification = TalentClassification::fromSlug($slug);
            if ($classification) {
                $setTalentType->execute($user, $classification, markCompleted: false);
            }
        }

        return $this->resumeOnboarding($user);
    }

    /**
     * Add a new classification to an existing user later.
     */
    public function addClassification(Request $request, \App\Actions\Profile\SetUserTalentTypeAction $setTalentType)
    {
        $validated = $request->validate([
            'classification' => 'required|in:' . implode(',', TalentClassification::slugs()),
        ]);

        $user = $request->user();
        $classification = TalentClassification::fromSlug($validated['classification']);

        if ($classification) {
            $setTalentType->execute($user, $classification, markCompleted: false);
            $user->update([
                'onboarding_completed'         => false,
                'talent_onboarding_started_at' => $user->talent_onboarding_started_at ?? now(),
            ]);
        }

        return $this->resumeOnboarding($user);
    }

    // ─── Step 2C: Professional Profile Base Form ──────────────────────────────

    /**
     * Step 2C: Professional & Artisan base profile setup page.
     */
    public function showProfessionalForm(Request $request)
    {
        $user = $request->user();
        $categories = Category::whereNotNull('parent_id')->with('parent')->get();
        if ($categories->isEmpty()) {
            $categories = Category::all();
        }
        $skills = Skill::orderBy('name')->get();

        return view('onboarding.professional', compact('user', 'categories', 'skills'));
    }

    /**
     * Submit professional base profile.
     */
    public function submitProfessionalForm(
        Request $request,
        UpdateProfessionalProfileAction $action,
        \App\Actions\Profile\SetUserTalentTypeAction $setTalentType
    ) {
        $user = $request->user();

        $hasTeacher      = $user->talentTypes()->where('slug', 'teacher')->exists();
        $hasSkilledLabour= $user->talentTypes()->where('slug', 'skilled_labour')->exists();
        $hasProfessional = $user->talentTypes()->where('slug', 'professional')->exists();

        $requiresStructuredLocation = $hasTeacher || $hasSkilledLabour;

        $validated = $request->validate([
            'category_id'            => 'required|exists:categories,id',
            'display_name'           => 'required|string|max:100',
            'bio'                    => 'required|string|min:20',
            'years_of_experience'    => 'required|integer|min:0|max:50',
            'location'               => 'required|string|max:100',
            'location_state'         => $requiresStructuredLocation ? 'required|string|max:100' : 'nullable|string|max:100',
            'location_city'          => $requiresStructuredLocation ? 'required|string|max:100' : 'nullable|string|max:100',
            'location_neighbourhood' => $requiresStructuredLocation ? 'required|string|max:100' : 'nullable|string|max:100',
            'location_landmark'      => 'nullable|string|max:500',
            'phone'                  => 'required|string|max:30',
            'skills'                 => 'nullable|array',
            'custom_skills'          => 'nullable|string|max:500',
        ]);
        $validated['custom_skills'] = $request->input('custom_skills');

        $forClassification = $hasProfessional ? TalentClassification::Professional : null;

        $action->execute($user, $validated, $forClassification);

        if ($hasProfessional) {
            $setTalentType->execute($user, TalentClassification::Professional, markCompleted: true);
        }

        return $this->resumeOnboarding($user);
    }

    // ─── Step 2D: Teacher / Tutor Detail Form ─────────────────────────────────

    /**
     * Step 2D: Academic tutor specialisation setup page.
     */
    public function showTutorForm(Request $request)
    {
        $user    = $request->user();
        $subjects = Subject::orderBy('name')->get();
        $levels  = EducationLevel::all();

        return view('onboarding.tutor', compact('user', 'subjects', 'levels'));
    }

    /**
     * Submit academic tutor specialisation.
     */
    public function submitTutorForm(
        Request $request,
        UpdateEducationProfileAction $action,
        \App\Actions\Profile\SetUserTalentTypeAction $setTalentType
    ) {
        $validated = $request->validate([
            'teaching_mode'   => 'required|in:physical,online,both',
            'qualifications'  => 'nullable|string|max:255',
            'rate_min'        => 'nullable|numeric|min:0',
            'rate_max'        => 'nullable|numeric|min:0',
            'subject_ids'     => 'required_without:custom_subjects|nullable|array',
            'custom_subjects' => 'nullable|string|max:500',
            'level_ids'       => 'required|array|min:1',
            'level_ids.*'     => 'exists:education_levels,id',
        ]);

        if (empty($validated['subject_ids']) && empty(trim($request->input('custom_subjects', '')))) {
            return redirect()->back()->withInput()->withErrors(['subject_ids' => 'Please select at least one subject or specify custom subjects.']);
        }

        $validated['custom_subjects'] = $request->input('custom_subjects');

        $user = $request->user();
        $action->execute($user, $validated);
        $setTalentType->execute($user, TalentClassification::Teacher, markCompleted: true);

        return $this->resumeOnboarding($user);
    }

    // ─── Step 2E: Skilled Labour Detail Form ──────────────────────────────────

    /**
     * Step 2E: Skilled Labour specialisation setup page.
     */
    public function showSkilledLabourForm(Request $request)
    {
        $user   = $request->user();
        $skills = Skill::orderBy('name')->get();

        // Trade categories are children of "Home & Technical Services"
        $tradeParent      = Category::where('slug', 'home-technical-services')->first();
        $tradeCategories  = $tradeParent
            ? Category::where('parent_id', $tradeParent->id)->orderBy('name')->get()
            : Category::all();

        return view('onboarding.skilled-labour', compact('user', 'skills', 'tradeCategories'));
    }

    /**
     * Submit Skilled Labour specialisation.
     */
    public function submitSkilledLabourForm(
        Request $request,
        UpdateSkilledLabourProfileAction $action,
        \App\Actions\Profile\SetUserTalentTypeAction $setTalentType
    ) {
        $validated = $request->validate([
            'trade_category_id'      => 'nullable|exists:categories,id',
            'is_certified'           => 'boolean',
            'certification_notes'    => 'nullable|string|max:500',
            'display_name'           => 'nullable|string|max:100',
            'bio'                    => 'nullable|string|min:20',
            'location'               => 'nullable|string|max:100',
            'location_state'         => 'nullable|string|max:100',
            'location_city'          => 'nullable|string|max:100',
            'location_neighbourhood' => 'nullable|string|max:100',
            'location_landmark'      => 'nullable|string|max:500',
            'years_of_experience'    => 'nullable|integer|min:0|max:50',
            'phone'                  => 'nullable|string|max:30',
            'skills'                 => 'nullable|array',
            'custom_skills'          => 'nullable|string|max:500',
        ]);
        $validated['custom_skills'] = $request->input('custom_skills');

        $validated['is_certified'] = $request->boolean('is_certified');

        $user = $request->user();
        $action->execute($user, $validated);
        $setTalentType->execute($user, TalentClassification::SkilledLabour, markCompleted: true);

        return $this->resumeOnboarding($user);
    }

    // ─── Skip ─────────────────────────────────────────────────────────────────

    /**
     * Skip talent onboarding ("Skip for now").
     * Sets onboarding_completed = true so the user can access all platform features.
     * No talent_type_user rows are created.
     */
    public function skip(Request $request, UpdateCustomerProfileAction $action)
    {
        $action->skip($request->user());

        return redirect()->to('/dashboard')
            ->with('status', 'You can always add your service profile later from your dashboard.');
    }
}
