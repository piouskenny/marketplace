<?php

namespace App\Services;

use App\Models\Opportunity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Service for opportunity listing, filtering, and search.
 *
 * Encapsulates discovery queries for opportunities across all categories,
 * including education-specific filters. Shared between Livewire and future
 * API controllers.
 */
class OpportunityDiscoveryService
{
    /**
     * Search and filter opportunities.
     *
     * @param array{
     *     keyword?: string,
     *     category_id?: int,
     *     category?: string,
     *     location?: string,
     *     opportunity_type?: string,
     *     subject_id?: int,
     *     education_level_id?: int,
     *     teaching_mode?: string,
     * } $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Opportunity::query()
            ->with([
                'user',
                'category',
                'educationOpportunityDetails.subject',
                'educationOpportunityDetails.educationLevel',
            ]);

        $likeOp = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

        // Keyword Search
        if (!empty($filters['keyword'])) {
            $kw = trim($filters['keyword']);
            $query->where(function (Builder $q) use ($kw, $likeOp) {
                $q->where('title', $likeOp, "%{$kw}%")
                  ->orWhere('description', $likeOp, "%{$kw}%")
                  ->orWhere('location', $likeOp, "%{$kw}%")
                  ->orWhereHas('category', function (Builder $cq) use ($kw, $likeOp) {
                      $cq->where('name', $likeOp, "%{$kw}%");
                  });
            });
        }

        // Category Filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        } elseif (!empty($filters['category']) && $filters['category'] !== 'All') {
            $cat = $filters['category'];
            $query->whereHas('category', function (Builder $cq) use ($cat, $likeOp) {
                $cq->where('name', $likeOp, "%{$cat}%");
            });
        }

        // Location Filter
        if (!empty($filters['location']) && $filters['location'] !== 'All') {
            $loc = $filters['location'];
            $query->where('location', $likeOp, "%{$loc}%");
        }

        // Opportunity Type Filter
        if (!empty($filters['opportunity_type']) && $filters['opportunity_type'] !== 'All') {
            $query->where('opportunity_type', $filters['opportunity_type']);
        }

        // Education Details Filters
        if (!empty($filters['subject_id'])) {
            $subId = (int) $filters['subject_id'];
            $query->whereHas('educationOpportunityDetails', function (Builder $eq) use ($subId) {
                $eq->where('subject_id', $subId);
            });
        }

        if (!empty($filters['education_level_id'])) {
            $lvlId = (int) $filters['education_level_id'];
            $query->whereHas('educationOpportunityDetails', function (Builder $eq) use ($lvlId) {
                $eq->where('education_level_id', $lvlId);
            });
        }

        if (!empty($filters['teaching_mode']) && $filters['teaching_mode'] !== 'All') {
            $mode = strtolower(trim($filters['teaching_mode']));
            $query->whereHas('educationOpportunityDetails', function (Builder $eq) use ($mode) {
                if ($mode === 'both') {
                    $eq->whereIn('teaching_mode', ['both', 'physical', 'online']);
                } else {
                    $eq->whereIn('teaching_mode', [$mode, 'both']);
                }
            });
        }

        return $query->where('status', 'open')->orderBy('created_at', 'desc')->paginate($perPage);
    }
}

