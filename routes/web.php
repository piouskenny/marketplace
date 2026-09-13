<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

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

    // Onboarding Flow
    Route::get('/onboarding', [App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding');
    Route::get('/onboarding/client', [App\Http\Controllers\OnboardingController::class, 'showClientForm'])->name('onboarding.client');
    Route::post('/onboarding/client', [App\Http\Controllers\OnboardingController::class, 'submitClientForm']);

    Route::get('/onboarding/professional', [App\Http\Controllers\OnboardingController::class, 'showProfessionalForm'])->name('onboarding.professional');
    Route::post('/onboarding/professional', [App\Http\Controllers\OnboardingController::class, 'submitProfessionalForm']);

    Route::get('/onboarding/tutor', [App\Http\Controllers\OnboardingController::class, 'showTutorForm'])->name('onboarding.tutor');
    Route::post('/onboarding/tutor', [App\Http\Controllers\OnboardingController::class, 'submitTutorForm']);

    // Profile Management Hub
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [App\Http\Controllers\ProfileController::class, 'update']);
});

