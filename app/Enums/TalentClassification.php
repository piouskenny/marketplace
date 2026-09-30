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
            self::Professional  => 'Professional',
            self::Teacher       => 'Teacher',
            self::SkilledLabour => 'Skilled Labour Worker',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Professional  => 'Software developers, accountants, designers, consultants, photographers and other professional service providers.',
            self::Teacher       => 'Mathematics tutors, English teachers, music instructors, WAEC/JAMB exam prep specialists and other educators.',
            self::SkilledLabour => 'Carpenters, plumbers, electricians, painters, welders, mechanics, tilers and other skilled trade workers.',
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
