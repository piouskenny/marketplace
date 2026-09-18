<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\EducationLevel;
use App\Models\Subject;
use App\Services\ProfessionalDiscoveryService;
use Illuminate\Http\Request;

class TalentController extends Controller
{
    /**
     * Display the Find Talent & Tutors Directory Page.
     */
    public function index(Request $request, ProfessionalDiscoveryService $discoveryService)
    {
        $searchQuery = trim($request->input('query', ''));
        $selectedCategory = trim($request->input('category', 'All'));
        $selectedLocation = trim($request->input('location', 'All'));
        $selectedSubject = $request->input('subject_id') ? (int) $request->input('subject_id') : null;
        $selectedLevel = $request->input('education_level_id') ? (int) $request->input('education_level_id') : null;
        $selectedTeachingMode = trim($request->input('teaching_mode', 'All'));
        $selectedMinRating = $request->input('min_rating') ? (float) $request->input('min_rating') : 0;

        $categories = Category::whereNull('parent_id')->get();
        $subjects = Subject::orderBy('name')->get();
        $educationLevels = EducationLevel::orderBy('id')->get();

        $filters = [
            'query' => $searchQuery,
            'category' => $selectedCategory,
            'location' => $selectedLocation,
            'subject_id' => $selectedSubject,
            'education_level_id' => $selectedLevel,
            'teaching_mode' => $selectedTeachingMode,
            'min_rating' => $selectedMinRating,
        ];

        $paginated = $discoveryService->search($filters, 50);
        $professionals = $paginated->items();

        return view('talent.index', compact(
            'professionals',
            'categories',
            'subjects',
            'educationLevels',
            'searchQuery',
            'selectedCategory',
            'selectedLocation',
            'selectedSubject',
            'selectedLevel',
            'selectedTeachingMode',
            'selectedMinRating'
        ));
    }
}

