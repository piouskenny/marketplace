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

        // 1. Update user account details & avatar
        $validatedUser = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $validatedUser['name'];
        if (array_key_exists('phone', $validatedUser)) {
            $user->phone = $validatedUser['phone'];
        }
        if (array_key_exists('location', $validatedUser)) {
            $user->location = $validatedUser['location'];
        }
        $user->save();

        // 2. If professional form data is present
        if ($request->has('category_id')) {
            $validatedPro = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'display_name' => 'nullable|string|max:100',
                'bio' => 'nullable|string',
                'years_of_experience' => 'nullable|integer|min:0',
                'skills' => 'nullable|array',
            ]);

            $validatedPro['display_name'] = !empty($validatedPro['display_name']) ? $validatedPro['display_name'] : $user->name;
            $validatedPro['bio'] = !empty($validatedPro['bio']) ? $validatedPro['bio'] : 'Service provider on Skill Marketplace.';
            $validatedPro['years_of_experience'] = isset($validatedPro['years_of_experience']) ? (int)$validatedPro['years_of_experience'] : 1;
            $validatedPro['location'] = $user->location;
            $validatedPro['phone'] = $user->phone;

            $proAction->execute($user, $validatedPro);
        }

        // 3. If education tutor form data is present
        if ($request->has('subject_ids') || $request->has('level_ids') || $request->has('teaching_mode')) {
            $validatedEdu = $request->validate([
                'teaching_mode' => 'nullable|in:physical,online,both',
                'qualifications' => 'nullable|string|max:255',
                'rate_min' => 'nullable|numeric|min:0',
                'rate_max' => 'nullable|numeric|min:0',
                'subject_ids' => 'nullable|array',
                'level_ids' => 'nullable|array',
            ]);

            $validatedEdu['teaching_mode'] = $validatedEdu['teaching_mode'] ?? 'both';
            $validatedEdu['subject_ids'] = $validatedEdu['subject_ids'] ?? [];
            $validatedEdu['level_ids'] = $validatedEdu['level_ids'] ?? [];

            $eduAction->execute($user, $validatedEdu);
        }

        return redirect()->back()->with('status', 'Profile details and avatar updated successfully!');
    }
}
