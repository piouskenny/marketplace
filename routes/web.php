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

// Dashboard Route (Protected for logged in users)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
