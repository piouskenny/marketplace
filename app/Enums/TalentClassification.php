<?php

namespace App\Enums;

enum TalentClassification: string
{
    case Professional  = 'professional';
    case Teacher       = 'teacher';
    case SkilledLabour = 'skilled_labour';

    public function label(): string
    {
        return match ($this) {
            self::Professional  => 'Business & Professional Services',
            self::Teacher       => 'Teaching & Education',
            self::SkilledLabour => 'Skilled Trades & Services',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Professional  => 'Consulting, technology, design, marketing, finance and other professional services.',
            self::Teacher       => 'Teaching, tutoring, training and other education services.',
            self::SkilledLabour => 'Technical, artisan, craft and other skilled services.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Professional  => 'briefcase',
            self::Teacher       => 'academic-cap',
            self::SkilledLabour => 'wrench-screwdriver',
        };
    }

    /**
     * Find by slug string, or return null if not found.
     */
    public static function fromSlug(string $slug): ?self
    {
        return match ($slug) {
            'professional'   => self::Professional,
            'teacher'        => self::Teacher,
            'skilled_labour' => self::SkilledLabour,
            default          => null,
        };
    }

    /**
     * All classification slug values (for validation rules).
     *
     * @return array<string>
     */
    public static function slugs(): array
    {
        return array_column(self::cases(), 'value');
    }
}
