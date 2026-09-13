<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProfessionalProfile;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = trim($request->input('query', ''));
        $selectedCategory = trim($request->input('category', 'All'));

        $categories = Category::whereNull('parent_id')->with('children')->get();

        $query = ProfessionalProfile::with([
            'user',
            'category',
            'skills',
            'educationProfile.subjects',
            'educationProfile.educationLevels',
        ]);

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('display_name', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('bio', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('location', 'LIKE', "%{$searchQuery}%")
                  ->orWhereHas('user', function ($uq) use ($searchQuery) {
                      $uq->where('name', 'LIKE', "%{$searchQuery}%")
                         ->orWhere('location', 'LIKE', "%{$searchQuery}%");
                  })
                  ->orWhereHas('category', function ($cq) use ($searchQuery) {
                      $cq->where('name', 'LIKE', "%{$searchQuery}%");
                  })
                  ->orWhereHas('skills', function ($sq) use ($searchQuery) {
                      $sq->where('name', 'LIKE', "%{$searchQuery}%");
                  })
                  ->orWhereHas('educationProfile.subjects', function ($subq) use ($searchQuery) {
                      $subq->where('name', 'LIKE', "%{$searchQuery}%");
                  });
            });
        }

        if (!empty($selectedCategory) && $selectedCategory !== 'All') {
            $query->where(function ($q) use ($selectedCategory) {
                $q->whereHas('category', function ($cq) use ($selectedCategory) {
                    $cq->where('name', 'LIKE', "%{$selectedCategory}%")
                       ->orWhere('slug', 'LIKE', "%{$selectedCategory}%");
                })->orWhereHas('category.parent', function ($pq) use ($selectedCategory) {
                    $pq->where('name', 'LIKE', "%{$selectedCategory}%");
                });
            });
        }

        $professionals = $query->orderBy('average_rating', 'desc')->get();

        return view('index', compact('categories', 'professionals', 'searchQuery', 'selectedCategory'));
    }
}
