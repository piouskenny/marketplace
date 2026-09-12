<?php

namespace App\DataTransferObjects;

/**
 * Immutable data object for creating a connection request.
 */
final readonly class CreateConnectionRequestData
{
    public function __construct(
        public int $initiatorId,
        public int $recipientId,
        public string $type,
        public ?int $professionalProfileId = null,
        public ?int $opportunityId = null,
        public ?string $message = null,
    ) {}
}
