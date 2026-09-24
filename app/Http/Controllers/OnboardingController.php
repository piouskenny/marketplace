<?php

namespace App\Http\Controllers;

use App\Actions\Profile\UpdateCustomerProfileAction;
use App\Actions\Profile\UpdateEducationProfileAction;
use App\Actions\Profile\UpdateProfessionalProfileAction;
use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Skill;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    /**
     * Step 1: Onboarding Intent Selection Page
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return view('onboarding.index', compact('user'));
    }

    /**
     * Step 2A: Customer / Hirer Setup Page
     */
    public function showClientForm(Request $request)
    {
        $user = $request->user();

        return view('onboarding.client', compact('user'));
    }

    /**
     * Submit Customer / Hirer Profile
     */
    public function submitClientForm(Request $request, UpdateCustomerProfileAction $action)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:30',
            'location' => 'required|string|max:100',
        ]);

        $action->execute($request->user(), $validated);

        return redirect()->to('/dashboard')->with('status', 'Account setup complete! You can now explore professionals or post opportunities.');
    }

    /**
     * Step 2B: Professional & Artisan Setup Page
     */
    public function showProfessionalForm(Request $request)
    {
        $user = $request->user();
        $categories = Category::whereNotNull('parent_id')->with('parent')->get();
        if ($categories->isEmpty()) {
            $categories = Category::all();
        }
        $skills = Skill::all();

        return view('onboarding.professional', compact('user', 'categories', 'skills'));
    }

    /**
     * Submit Professional Profile
     */
    public function submitProfessionalForm(Request $request, UpdateProfessionalProfileAction $action)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'display_name' => 'required|string|max:100',
            'bio' => 'required|string|min:20',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'location' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        $profile = $action->execute($request->user(), $validated);
        $category = Category::find($validated['category_id']);

        // Check if category is Education & Tutoring vertical
        $isEducationCategory = Str::contains(Str::lower($category->name ?? ''), ['education', 'tutor', 'academic'])
            || Str::contains(Str::lower($category->parent->name ?? ''), ['education', 'tutor', 'academic']);

        if ($isEducationCategory) {
            return redirect()->to('/onboarding/tutor')->with('status', 'Basic profile saved! Please complete your academic tutoring details.');
        }

        return redirect()->to('/dashboard')->with('status', 'Professional profile created! Welcome to Skill Link NG.');
    }

    /**
     * Step 2C: Academic Tutor Setup Page
     */
    public function showTutorForm(Request $request)
    {
        $user = $request->user();
        $subjects = Subject::all();
        $levels = EducationLevel::all();

        return view('onboarding.tutor', compact('user', 'subjects', 'levels'));
    }

    /**
     * Submit Academic Tutor Profile
     */
    public function submitTutorForm(Request $request, UpdateEducationProfileAction $action)
    {
        $validated = $request->validate([
            'teaching_mode' => 'required|in:physical,online,both',
            'qualifications' => 'nullable|string|max:255',
            'rate_min' => 'nullable|numeric|min:0',
            'rate_max' => 'nullable|numeric|min:0',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
            'level_ids' => 'required|array|min:1',
            'level_ids.*' => 'exists:education_levels,id',
        ]);

        $action->execute($request->user(), $validated);

        return redirect()->to('/dashboard')->with('status', 'Academic tutor profile complete! You are now listed in the Tutoring Directory.');
    }
}
