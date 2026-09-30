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
        $selectedTalentType = trim($request->input('talent_type', 'All'));
        $selectedCategory = trim($request->input('category', 'All'));
        $selectedLocation = trim($request->input('location', 'All'));
        $selectedState = trim($request->input('location_state', $request->input('state', '')));
        $selectedCity = trim($request->input('location_city', $request->input('city', '')));
        $selectedNeighbourhood = trim($request->input('location_neighbourhood', $request->input('neighbourhood', '')));
        $strictLocation = $request->boolean('strict_location', false);

        $selectedSubject = $request->input('subject_id') ? (int) $request->input('subject_id') : null;
        $selectedLevel = $request->input('education_level_id') ? (int) $request->input('education_level_id') : null;
        $selectedTeachingMode = trim($request->input('teaching_mode', 'All'));

        $selectedTradeCategory = $request->input('trade_category_id') ? (int) $request->input('trade_category_id') : null;
        $selectedSkill = trim($request->input('skill', ''));
        $selectedMinRating = $request->input('min_rating') ? (float) $request->input('min_rating') : 0;
        $selectedSort = trim($request->input('sort', 'rating_desc'));

        $categories = Category::whereNull('parent_id')->get();
        $tradeCategories = Category::whereNotNull('parent_id')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $educationLevels = EducationLevel::orderBy('id')->get();

        $filters = [
            'query' => $searchQuery,
            'talent_type' => $selectedTalentType,
            'category' => $selectedCategory,
            'location' => $selectedLocation,
            'location_state' => $selectedState,
            'location_city' => $selectedCity,
            'location_neighbourhood' => $selectedNeighbourhood,
            'strict_location' => $strictLocation,
            'subject_id' => $selectedSubject,
            'education_level_id' => $selectedLevel,
            'teaching_mode' => $selectedTeachingMode,
            'trade_category_id' => $selectedTradeCategory,
            'skill' => $selectedSkill,
            'min_rating' => $selectedMinRating,
            'sort' => $selectedSort,
        ];

        $paginated = $discoveryService->search($filters, 50);
        $professionals = $paginated->items();

        return view('talent.index', compact(
            'paginated',
            'professionals',
            'categories',
            'tradeCategories',
            'subjects',
            'educationLevels',
            'searchQuery',
            'selectedTalentType',
            'selectedCategory',
            'selectedLocation',
            'selectedState',
            'selectedCity',
            'selectedNeighbourhood',
            'strictLocation',
            'selectedSubject',
            'selectedLevel',
            'selectedTeachingMode',
            'selectedTradeCategory',
            'selectedSkill',
            'selectedMinRating',
            'selectedSort'
        ));
    }
}
