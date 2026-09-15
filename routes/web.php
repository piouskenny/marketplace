<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TalentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/talent', [TalentController::class, 'index'])->name('talent.index');

// Auth Routes
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Google Auth Redirect
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');

// Logout Route
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Protected Routes for Authenticated Users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/talent', [DashboardController::class, 'talent'])->name('dashboard.talent');
    Route::get('/dashboard/messages', [DashboardController::class, 'messages'])->name('dashboard.messages');
    Route::get('/dashboard/my-jobs', [OpportunityController::class, 'myJobs'])->name('dashboard.my-jobs');
    Route::post('/opportunities', [OpportunityController::class, 'store'])->name('opportunities.store');
    Route::delete('/opportunities/{opportunity}', [OpportunityController::class, 'destroy'])->name('opportunities.destroy');
    Route::post('/opportunities/{opportunity}/apply', [ConnectionController::class, 'apply'])->name('opportunities.apply');
    Route::post('/connections/{connection}/accept', [ConnectionController::class, 'accept'])->name('connections.accept');
    Route::post('/connections/{connection}/reject', [ConnectionController::class, 'reject'])->name('connections.reject');
    Route::post('/connections/{connection}/pay', [ConnectionController::class, 'pay'])->name('connections.pay');
    Route::post('/connections/{connection}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('connections.review');
    Route::get('/connections/status', [ConnectionController::class, 'status'])->name('connections.status');

    // Notifications Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Chat & Messaging Routes
    Route::get('/conversations', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.send');
    Route::post('/conversations/{conversation}/read', [App\Http\Controllers\ChatController::class, 'markRead'])->name('chat.read');

    // Email Verification Routes
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');

    // Verified Email Protected Routes
    Route::middleware('verified')->group(function () {
        // Onboarding Flow
        Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
        Route::get('/onboarding/client', [OnboardingController::class, 'showClientForm'])->name('onboarding.client');
        Route::post('/onboarding/client', [OnboardingController::class, 'submitClientForm']);

        Route::get('/onboarding/professional', [OnboardingController::class, 'showProfessionalForm'])->name('onboarding.professional');
        Route::post('/onboarding/professional', [OnboardingController::class, 'submitProfessionalForm']);

        Route::get('/onboarding/tutor', [OnboardingController::class, 'showTutorForm'])->name('onboarding.tutor');
        Route::post('/onboarding/tutor', [OnboardingController::class, 'submitTutorForm']);

        // Profile Management Hub
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/edit', [ProfileController::class, 'update']);
    });
});


