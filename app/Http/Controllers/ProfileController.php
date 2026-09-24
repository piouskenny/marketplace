<?php

namespace App\Http\Controllers;

use App\Actions\Profile\UpdateEducationProfileAction;
use App\Actions\Profile\UpdateProfessionalProfileAction;
use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Skill;
use App\Models\Subject;
use App\Models\User;
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

        $authUser = $request->user();
        $userNotifications = $authUser ? $authUser->notifications()->take(15)->get() : collect();
        $unreadCount = $authUser ? $authUser->unreadNotifications()->count() : 0;

        return view('profile.edit', compact('user', 'categories', 'skills', 'subjects', 'levels', 'userNotifications', 'unreadCount'));
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
            'job_alerts_enabled' => 'nullable|boolean',
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
        if ($request->has('job_alerts_enabled')) {
            $user->job_alerts_enabled = (bool) $request->input('job_alerts_enabled');
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
            $validatedPro['bio'] = !empty($validatedPro['bio']) ? $validatedPro['bio'] : 'Service provider on Skill Link NG.';
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

    /**
     * Display Standalone User Profile Page
     */
    public function show(Request $request, User $user)
    {
        $user->loadMissing([
            'professionalProfile.category',
            'professionalProfile.skills',
            'professionalProfile.educationProfile.subjects',
            'professionalProfile.educationProfile.educationLevels',
            'reviewsReceived.reviewer',
            'opportunities',
        ]);

        /** @var \App\Models\User|null $authUser */
        $authUser = $request->user();

        $isOwnProfile = $authUser && ((int)$authUser->id === (int)$user->id);

        $sessionPaid = session()->get('paid_connections', []);
        $sessionAccepted = session()->get('accepted_connections', []);

        // Search DB connection request between authUser and target user
        $connection = null;
        if ($authUser && !$isOwnProfile) {
            $connection = \App\Models\ConnectionRequest::where(function ($q) use ($authUser, $user) {
                $q->where('initiator_id', $authUser->id)->where('recipient_id', $user->id);
            })->orWhere(function ($q) use ($authUser, $user) {
                $q->where('initiator_id', $user->id)->where('recipient_id', $authUser->id);
            })->latest()->first();
        }

        $dbStatus = null;
        if ($connection) {
            $dbStatus = $connection->status instanceof \App\Enums\ConnectionStatus 
                ? $connection->status->value 
                : (string) $connection->status;
        }

        $hasActiveConnection = false;
        if ($isOwnProfile) {
            $hasActiveConnection = true;
        } elseif ($connection) {
            if ($dbStatus === \App\Enums\ConnectionStatus::Connected->value || $connection->connected_at !== null || in_array($connection->id, $sessionPaid)) {
                $hasActiveConnection = true;
            }
        }

        // Mask phone & email if not connected & not own profile
        if ($hasActiveConnection) {
            $maskedPhone = $user->phone ?? 'Not specified';
            $maskedEmail = $user->email;
            $isContactUnlocked = true;
        } else {
            $isContactUnlocked = false;
            $phoneRaw = $user->phone;
            if ($phoneRaw && strlen($phoneRaw) > 4) {
                $maskedPhone = substr($phoneRaw, 0, 4) . ' ••• ••• ' . substr($phoneRaw, -2);
            } else {
                $maskedPhone = '••••••••••';
            }

            $emailRaw = $user->email;
            $parts = explode('@', $emailRaw);
            $namePart = $parts[0] ?? 'user';
            $domainPart = $parts[1] ?? 'example.com';
            $maskedEmail = (strlen($namePart) > 2 ? substr($namePart, 0, 2) : substr($namePart, 0, 1)) . '••••@••••' . strrchr($domainPart, '.');
        }

        $profile = $user->professionalProfile;
        $rating = $profile ? ($profile->average_rating ?? 5.0) : 5.0;
        $reviews = $user->reviewsReceived()->with('reviewer')->latest()->get();

        $authUser = auth()->user();
        $userNotifications = $authUser ? $authUser->notifications()->take(15)->get() : collect();
        $unreadCount = $authUser ? $authUser->unreadNotifications()->count() : 0;

        return view('profile.show', compact(
            'user',
            'profile',
            'isOwnProfile',
            'hasActiveConnection',
            'isContactUnlocked',
            'maskedPhone',
            'maskedEmail',
            'connection',
            'dbStatus',
            'rating',
            'reviews',
            'userNotifications',
            'unreadCount'
        ));
    }

    /**
     * Unsubscribe user from job alert emails (Signed URL)
     */
    public function unsubscribeJobAlerts(Request $request, User $user)
    {
        $user->job_alerts_enabled = false;
        $user->save();

        return view('emails.job-alerts-unsubscribed', compact('user'));
    }
}

