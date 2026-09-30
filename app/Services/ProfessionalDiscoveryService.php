<?php

namespace App\Services;

use App\Models\ProfessionalProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Service for professional profile discovery and search queries.
 *
 * Encapsulates complex query logic (filtering, sorting, location relevance, pagination)
 * shared between web directory controllers, dashboard views, and future API endpoints.
 *
 * All talent types (Professional, Teacher, Skilled Labour) are discovered
 * through this service since all have a professional_profiles base record.
 * Single source of truth for business classifications is the talent_type_user pivot table.
 * Location filtering uses structured location fields on professional_profiles.
 */
class ProfessionalDiscoveryService
{
    /**
     * Search and filter professional profiles.
     *
     * @param array{
     *     query?: string,
     *     talent_type?: string,
     *     category?: string,
     *     category_id?: int,
     *     trade_category_id?: int,
     *     skill?: string,
     *     skill_id?: int,
     *     location?: string,
     *     location_state?: string,
     *     location_city?: string,
     *     location_neighbourhood?: string,
     *     state?: string,
     *     city?: string,
     *     neighbourhood?: string,
     *     strict_location?: bool,
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
                'user.talentTypes',
                'category',
                'skills',
                'educationProfile.subjects',
                'educationProfile.educationLevels',
                'skilledLabourProfile.tradeCategory',
            ]);

        $driver = DB::getDriverName();
        $likeOp = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';

        // ── Step 2: Talent Classification Filter & Completion Guard ─────────────
        // Single source of truth for business classification is talent_type_user pivot.
        // Public discovery only shows profiles with completed_at IS NOT NULL.
        if (!empty($filters['talent_type']) && $filters['talent_type'] !== 'All') {
            $talentTypeSlug = strtolower(trim($filters['talent_type']));
            $query->whereHas('user.talentTypes', function (Builder $tq) use ($talentTypeSlug) {
                $tq->where('talent_types.slug', $talentTypeSlug)
                   ->whereNotNull('talent_type_user.completed_at');
            });
        } else {
            // When 'All' or no talent_type specified, require at least one completed classification
            $query->whereHas('user.talentTypes', function (Builder $tq) {
                $tq->whereNotNull('talent_type_user.completed_at');
            });
        }

        // ── Step 3 & 4: Specialization Table Safety Guards ────────────────────
        if (!empty($filters['talent_type'])) {
            $type = strtolower(trim($filters['talent_type']));
            if ($type === 'teacher') {
                $query->has('educationProfile');
            } elseif ($type === 'skilled_labour') {
                $query->has('skilledLabourProfile');
            }
        }

        // ── Step 4: Skilled Labour Specific Filters ───────────────────────────
        if (!empty($filters['trade_category_id'])) {
            $tradeId = (int) $filters['trade_category_id'];
            $query->whereHas('skilledLabourProfile', function (Builder $sq) use ($tradeId) {
                $sq->where('trade_category_id', $tradeId);
            });
        }

        if (!empty($filters['skill_id'])) {
            $skillId = (int) $filters['skill_id'];
            $query->whereHas('skills', function (Builder $sq) use ($skillId) {
                $sq->where('skills.id', $skillId);
            });
        } elseif (!empty($filters['skill'])) {
            $skillName = trim($filters['skill']);
            $query->whereHas('skills', function (Builder $sq) use ($skillName, $likeOp) {
                $sq->where('name', $likeOp, "%{$skillName}%");
            });
        }

        // ── Keyword Search ─────────────────────────────────────────────────────
        if (!empty($filters['query'])) {
            $search = trim($filters['query']);
            $query->where(function (Builder $q) use ($search, $likeOp) {
                $q->where('display_name', $likeOp, "%{$search}%")
                  ->orWhere('bio', $likeOp, "%{$search}%")
                  ->orWhere('location', $likeOp, "%{$search}%")
                  ->orWhere('location_city', $likeOp, "%{$search}%")
                  ->orWhere('location_state', $likeOp, "%{$search}%")
                  ->orWhere('location_neighbourhood', $likeOp, "%{$search}%")
                  ->orWhereHas('user', function (Builder $uq) use ($search, $likeOp) {
                      $uq->where('name', $likeOp, "%{$search}%");
                  })
                  ->orWhereHas('category', function (Builder $cq) use ($search, $likeOp) {
                      $cq->where('name', $likeOp, "%{$search}%");
                  })
                  ->orWhereHas('skills', function (Builder $sq) use ($search, $likeOp) {
                      $sq->where('name', $likeOp, "%{$search}%");
                  })
                  ->orWhereHas('educationProfile.subjects', function (Builder $subq) use ($search, $likeOp) {
                      $subq->where('name', $likeOp, "%{$search}%");
                  })
                  ->orWhereHas('skilledLabourProfile.tradeCategory', function (Builder $tq) use ($search, $likeOp) {
                      $tq->where('name', $likeOp, "%{$search}%");
                  });
            });
        }

        // ── Category Filter ────────────────────────────────────────────────────
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        } elseif (!empty($filters['category']) && $filters['category'] !== 'All') {
            $cat = trim($filters['category']);
            $query->whereHas('category', function (Builder $cq) use ($cat, $likeOp) {
                $cq->where('name', $likeOp, "%{$cat}%")
                   ->orWhere('slug', $likeOp, "%{$cat}%");
            });
        }

        // ── Step 5, 6, 7, 8, 9: Structured Location & Relevance Scoring ──────
        $state = !empty($filters['location_state']) ? trim($filters['location_state']) : (!empty($filters['state']) ? trim($filters['state']) : null);
        $city = !empty($filters['location_city']) ? trim($filters['location_city']) : (!empty($filters['city']) ? trim($filters['city']) : null);
        $neighbourhood = !empty($filters['location_neighbourhood']) ? trim($filters['location_neighbourhood']) : (!empty($filters['neighbourhood']) ? trim($filters['neighbourhood']) : null);
        $legacyLoc = !empty($filters['location']) && $filters['location'] !== 'All' ? trim($filters['location']) : null;

        $isStrictLocation = !empty($filters['strict_location']);
        $hasStructuredFilter = !empty($state) || !empty($city) || !empty($neighbourhood);

        if ($hasStructuredFilter) {
            if ($isStrictLocation) {
                // Strict location matching
                if (!empty($neighbourhood)) {
                    $query->whereRaw('LOWER(location_neighbourhood) = LOWER(?)', [$neighbourhood]);
                }
                if (!empty($city)) {
                    $query->whereRaw('LOWER(location_city) = LOWER(?)', [$city]);
                }
                if (!empty($state)) {
                    $query->whereRaw('LOWER(location_state) = LOWER(?)', [$state]);
                }
            } else {
                // Relevance ranking mode with fallback
                $query->where(function (Builder $lq) use ($state, $city, $neighbourhood, $likeOp) {
                    if (!empty($state)) {
                        $lq->orWhere('location_state', $likeOp, "%{$state}%");
                    }
                    if (!empty($city)) {
                        $lq->orWhere('location_city', $likeOp, "%{$city}%");
                    }
                    if (!empty($neighbourhood)) {
                        $lq->orWhere('location_neighbourhood', $likeOp, "%{$neighbourhood}%");
                    }
                });

                // Calculate location_score (3 = neighbourhood, 2 = city, 1 = state, 0 = other)
                $targetNeighbourhood = $neighbourhood ?? '';
                $targetCity = $city ?? '';
                $targetState = $state ?? '';

                $query->selectRaw('professional_profiles.*, (
                    CASE
                        WHEN LOWER(location_neighbourhood) = LOWER(?) AND LOWER(location_neighbourhood) != \'\' THEN 3
                        WHEN LOWER(location_city) = LOWER(?) AND LOWER(location_city) != \'\' THEN 2
                        WHEN LOWER(location_state) = LOWER(?) AND LOWER(location_state) != \'\' THEN 1
                        ELSE 0
                    END
                ) AS location_score', [$targetNeighbourhood, $targetCity, $targetState]);

                $query->orderByDesc('location_score');
            }
        } elseif ($legacyLoc) {
            // Free-text location fallback (legacy compatibility)
            $query->where(function (Builder $lq) use ($legacyLoc, $likeOp) {
                $lq->where('location', $likeOp, "%{$legacyLoc}%")
                   ->orWhere('location_city', $likeOp, "%{$legacyLoc}%")
                   ->orWhere('location_state', $likeOp, "%{$legacyLoc}%")
                   ->orWhere('location_neighbourhood', $likeOp, "%{$legacyLoc}%");
            });
        }

        // ── Minimum Rating Filter ──────────────────────────────────────────────
        if (!empty($filters['min_rating']) && (float) $filters['min_rating'] > 0) {
            $query->where('average_rating', '>=', (float) $filters['min_rating']);
        }

        // ── Step 3: Education Subject Filter ──────────────────────────────────
        if (!empty($filters['subject_id'])) {
            $subjectId = (int) $filters['subject_id'];
            $query->whereHas('educationProfile.subjects', function (Builder $subq) use ($subjectId) {
                $subq->where('subjects.id', $subjectId);
            });
        }

        // ── Step 3: Education Level Filter ────────────────────────────────────
        if (!empty($filters['education_level_id'])) {
            $levelId = (int) $filters['education_level_id'];
            $query->whereHas('educationProfile.educationLevels', function (Builder $lvlq) use ($levelId) {
                $lvlq->where('education_levels.id', $levelId);
            });
        }

        // ── Step 3: Teaching Mode Filter ──────────────────────────────────────
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

        // ── Step 10: Secondary Sorting ───────────────────────────────────────
        $sort = $filters['sort'] ?? 'rating_desc';
        match ($sort) {
            'experience_desc' => $query->orderBy('years_of_experience', 'desc'),
            'latest'          => $query->orderBy('created_at', 'desc'),
            default           => $query->orderBy('average_rating', 'desc')->orderBy('created_at', 'desc'),
        };

        $paginated = $query->paginate($perPage);
        $paginated->withQueryString();

        return $paginated;
    }
}
