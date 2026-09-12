<?php

namespace App\Services;

/**
 * Service for professional profile discovery and search queries.
 *
 * Encapsulates complex query logic (filtering, sorting, pagination)
 * that is shared between Livewire directory components and future API
 * endpoints. Does NOT perform authorization — that responsibility
 * belongs to policies and the calling layer.
 */
class ProfessionalDiscoveryService
{
    /**
     * Search/filter professional profiles.
     *
     * @param array{
     *     category_id?: int,
     *     skill_ids?: array<int>,
     *     location?: string,
     *     min_rating?: float,
     *     subject_id?: int,
     *     education_level_id?: int,
     *     teaching_mode?: string,
     *     query?: string,
     * } $filters
     *
     * Placeholder — will return a paginated query builder result.
     */
    public function search(array $filters = [], int $perPage = 15): mixed
    {
        // Intended implementation:
        //   Build an Eloquent query on ProfessionalProfile
        //   Apply filters conditionally using when()
        //   Join/filter education_profiles when education filters present
        //   Eager load category, skills, user (public fields only)
        //   Paginate and return

        throw new \RuntimeException('ProfessionalDiscoveryService::search is not yet implemented.');
    }
}
