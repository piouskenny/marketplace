<?php

namespace App\Services;

use App\Models\ProfessionalProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Service for professional profile discovery and search queries.
 *
 * Encapsulates complex query logic (filtering, sorting, pagination)
 * shared between web directory controllers and future API endpoints.
 */
class ProfessionalDiscoveryService
{
    /**
     * Search and filter professional profiles.
     *
     * @param array{
     *     query?: string,
     *     category?: string,
     *     category_id?: int,
     *     location?: string,
     *     min_rating?: float|int,
     *     subject_id?: int,
     *     education_level_id?: int,
     *     teaching_mode?: string,
     *     sort?: string,
     * } $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ProfessionalProfile::query()
            ->with([
                'user',
                'category',
                'skills',
                'educationProfile.subjects',
                'educationProfile.educationLevels',
            ]);

        // Keyword Search Query
        if (!empty($filters['query'])) {
            $search = trim($filters['query']);
            $query->where(function (Builder $q) use ($search) {
                $q->where('display_name', 'LIKE', "%{$search}%")
                  ->orWhere('bio', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function (Builder $uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('location', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('category', function (Builder $cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('skills', function (Builder $sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('educationProfile.subjects', function (Builder $subq) use ($search) {
                      $subq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Category Filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        } elseif (!empty($filters['category']) && $filters['category'] !== 'All') {
            $cat = $filters['category'];
            $query->whereHas('category', function (Builder $cq) use ($cat) {
                $cq->where('name', 'LIKE', "%{$cat}%")
                   ->orWhere('slug', 'LIKE', "%{$cat}%");
            });
        }

        // Location Filter
        if (!empty($filters['location']) && $filters['location'] !== 'All') {
            $loc = $filters['location'];
            $query->where('location', 'LIKE', "%{$loc}%");
        }

        // Minimum Rating Filter
        if (!empty($filters['min_rating']) && (float)$filters['min_rating'] > 0) {
            $minRating = (float) $filters['min_rating'];
            $query->where('average_rating', '>=', $minRating);
        }

        // Education Subject Filter
        if (!empty($filters['subject_id'])) {
            $subjectId = (int) $filters['subject_id'];
            $query->whereHas('educationProfile.subjects', function (Builder $subq) use ($subjectId) {
                $subq->where('subjects.id', $subjectId);
            });
        }

        // Education Level Filter
        if (!empty($filters['education_level_id'])) {
            $levelId = (int) $filters['education_level_id'];
            $query->whereHas('educationProfile.educationLevels', function (Builder $lvlq) use ($levelId) {
                $lvlq->where('education_levels.id', $levelId);
            });
        }

        // Teaching Mode Filter (physical, online, both)
        if (!empty($filters['teaching_mode']) && $filters['teaching_mode'] !== 'All') {
            $mode = strtolower(trim($filters['teaching_mode']));
            $query->whereHas('educationProfile', function (Builder $edq) use ($mode) {
                if ($mode === 'both') {
                    $edq->whereIn('teaching_mode', ['both', 'physical', 'online']);
                } else {
                    $edq->whereIn('teaching_mode', [$mode, 'both']);
                }
            });
        }

        // Sorting
        $sort = $filters['sort'] ?? 'rating_desc';
        if ($sort === 'experience_desc') {
            $query->orderBy('years_of_experience', 'desc');
        } elseif ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('average_rating', 'desc')->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }
}

