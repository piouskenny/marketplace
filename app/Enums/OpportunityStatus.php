<?php

namespace App\Enums;

enum OpportunityStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Filled = 'filled';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Open => 'Open',
            self::Filled => 'Filled',
            self::Closed => 'Closed',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Statuses visible in public discovery/search results.
     *
     * @return array<self>
     */
    public static function discoverable(): array
    {
        return [self::Open];
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Open]);
    }

    public function canAcceptApplications(): bool
    {
        return $this === self::Open;
    }
}
