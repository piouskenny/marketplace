<?php

namespace App\Http\Controllers;

use App\Enums\OpportunityStatus;
use App\Models\Category;
use App\Models\Opportunity;
use App\Models\Subject;
use App\Services\ProfessionalDiscoveryService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request, ProfessionalDiscoveryService $discoveryService)
    {
        $searchQuery = trim($request->input('query', ''));
        $selectedCategory = trim($request->input('category', 'All'));

        // Handle Guest / Visitor Search Location Preference
        $state = trim($request->input('location_state', $request->input('state', '')));
        $city = trim($request->input('location_city', $request->input('city', '')));
        $neighbourhood = trim($request->input('location_neighbourhood', $request->input('neighbourhood', '')));

        if (!empty($state) || !empty($city) || !empty($neighbourhood)) {
            $searchLocation = [
                'state' => $state,
                'city' => $city,
                'neighbourhood' => $neighbourhood,
            ];
            $request->session()->put('search_location', $searchLocation);
        } else {
            $searchLocation = $request->session()->get('search_location', []);
        }

        $hasSelectedLocation = !empty($searchLocation['state']) || !empty($searchLocation['city']) || !empty($searchLocation['neighbourhood']);

        $categories = Category::whereNull('parent_id')->with('children')->get();
        $tradeCategories = Category::whereNotNull('parent_id')->orderBy('name')->take(6)->get();
        $subjects = Subject::orderBy('name')->take(8)->get();

        // Section A: Skilled Labour Workers (Limit 6)
        $skilledFilters = array_merge($searchLocation, [
            'query' => $searchQuery,
            'talent_type' => 'skilled_labour',
        ]);
        $skilledLabourWorkers = $discoveryService->search($skilledFilters, 6)->items();

        // Section B: Teachers & Academic Tutors (Limit 6)
        $teacherFilters = array_merge($searchLocation, [
            'query' => $searchQuery,
            'talent_type' => 'teacher',
        ]);
        $teachers = $discoveryService->search($teacherFilters, 6)->items();

        // Featured Professionals Overview (Limit 6)
        $profFilters = array_merge($searchLocation, [
            'query' => $searchQuery,
            'category' => $selectedCategory,
        ]);
        $professionals = $discoveryService->search($profFilters, 6)->items();

        // Featured Jobs — 6 latest open opportunities for the home page
        $featuredJobs = Opportunity::with(['category', 'user', 'connectionRequests'])
            ->withCount('connectionRequests')
            ->where('status', OpportunityStatus::Open)
            ->latest()
            ->take(6)
            ->get();

        return view('index', compact(
            'categories',
            'tradeCategories',
            'subjects',
            'professionals',
            'skilledLabourWorkers',
            'teachers',
            'searchQuery',
            'selectedCategory',
            'searchLocation',
            'hasSelectedLocation',
            'featuredJobs'
        ));
    }
}
