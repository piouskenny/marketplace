<?php

namespace App\Http\Controllers;

use App\Actions\Review\CreateReviewAction;
use App\Models\ConnectionRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class ReviewController extends Controller
{
    /**
     * Submit a review for a connected request.
     */
    public function store(Request $request, $connectionId, CreateReviewAction $action)
    {
        $user = Auth::user();
        $cleanId = str_replace('conn_', '', $connectionId);
        $connectionRequest = ConnectionRequest::findOrFail($cleanId);

        if ($user->cannot('create', [\App\Models\Review::class, $connectionRequest])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reviews can only be submitted for active, connected requests once per participant.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Reviews can only be submitted for active, connected requests once per participant.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $review = $action->execute(
                $connectionRequest,
                $user->id,
                (int) $validated['rating'],
                $validated['comment'] ?? null
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! Your review has been submitted successfully.',
                    'review' => $review,
                ]);
            }

            return redirect()->back()->with('status', 'Thank you! Your review has been submitted successfully.');
        } catch (InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
