<?php

namespace App\Services;

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
     * Search/filter opportunities.
     *
     * @param array{
     *     category_id?: int,
     *     keyword?: string,
     *     location?: string,
     *     opportunity_type?: string,
     *     subject_id?: int,
     *     education_level_id?: int,
     *     teaching_mode?: string,
     * } $filters
     *
     * Placeholder — will return a paginated query builder result.
     */
    public function search(array $filters = [], int $perPage = 15): mixed
    {
        throw new \RuntimeException('OpportunityDiscoveryService::search is not yet implemented.');
    }
}
