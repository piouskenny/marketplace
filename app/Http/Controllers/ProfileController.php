<?php

namespace App\Http\Controllers;

use App\Actions\Profile\UpdateEducationProfileAction;
use App\Actions\Profile\UpdateProfessionalProfileAction;
use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Skill;
use App\Models\Subject;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Edit Profile Hub Page
     */
    public function edit(Request $request)
    {
        $user = $request->user()->load(['professionalProfile.category', 'professionalProfile.skills', 'professionalProfile.educationProfile.subjects', 'professionalProfile.educationProfile.educationLevels']);
        $categories = Category::all();
        $skills = Skill::all();
        $subjects = Subject::all();
        $levels = EducationLevel::all();

        return view('profile.edit', compact('user', 'categories', 'skills', 'subjects', 'levels'));
    }

    /**
     * Update Profile Hub Action
     */
    public function update(Request $request, UpdateProfessionalProfileAction $proAction, UpdateEducationProfileAction $eduAction)
    {
        $user = $request->user();

        // Update user account details
        $validatedUser = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'location' => 'required|string|max:100',
        ]);
        $user->update($validatedUser);

        // If professional form data is present
        if ($request->has('category_id')) {
            $validatedPro = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'display_name' => 'required|string|max:100',
                'bio' => 'required|string|min:20',
                'years_of_experience' => 'required|integer|min:0',
                'skills' => 'nullable|array',
            ]);
            $validatedPro['location'] = $user->location;
            $validatedPro['phone'] = $user->phone;

            $proAction->execute($user, $validatedPro);
        }

        // If education tutor form data is present
        if ($request->has('subject_ids')) {
            $validatedEdu = $request->validate([
                'teaching_mode' => 'required|in:physical,online,both',
                'qualifications' => 'nullable|string|max:255',
                'rate_min' => 'nullable|numeric|min:0',
                'rate_max' => 'nullable|numeric|min:0',
                'subject_ids' => 'required|array',
                'level_ids' => 'required|array',
            ]);

            $eduAction->execute($user, $validatedEdu);
        }

        return redirect()->back()->with('status', 'Profile details updated successfully!');
    }
}
