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

        $likeOp = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery, $likeOp) {
                $q->where('display_name', $likeOp, "%{$searchQuery}%")
                  ->orWhere('bio', $likeOp, "%{$searchQuery}%")
                  ->orWhere('location', $likeOp, "%{$searchQuery}%")
                  ->orWhereHas('user', function ($uq) use ($searchQuery, $likeOp) {
                      $uq->where('name', $likeOp, "%{$searchQuery}%")
                         ->orWhere('location', $likeOp, "%{$searchQuery}%");
                  })
                  ->orWhereHas('category', function ($cq) use ($searchQuery, $likeOp) {
                      $cq->where('name', $likeOp, "%{$searchQuery}%");
                  })
                  ->orWhereHas('skills', function ($sq) use ($searchQuery, $likeOp) {
                      $sq->where('name', $likeOp, "%{$searchQuery}%");
                  })
                  ->orWhereHas('educationProfile.subjects', function ($subq) use ($searchQuery, $likeOp) {
                      $subq->where('name', $likeOp, "%{$searchQuery}%");
                  });
            });
        }

        if (!empty($selectedCategory) && $selectedCategory !== 'All') {
            $query->where(function ($q) use ($selectedCategory, $likeOp) {
                $q->whereHas('category', function ($cq) use ($selectedCategory, $likeOp) {
                    $cq->where('name', $likeOp, "%{$selectedCategory}%")
                       ->orWhere('slug', $likeOp, "%{$selectedCategory}%");
                })->orWhereHas('category.parent', function ($pq) use ($selectedCategory, $likeOp) {
                    $pq->where('name', $likeOp, "%{$selectedCategory}%");
                });
            });
        }

        $professionals = $query->orderBy('average_rating', 'desc')->get();

        return view('index', compact('categories', 'professionals', 'searchQuery', 'selectedCategory'));
    }
}
