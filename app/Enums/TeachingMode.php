<?php

namespace App\Enums;

enum TeachingMode: string
{
    case Physical = 'physical';
    case Online = 'online';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Physical => 'Physical',
            self::Online => 'Online',
            self::Both => 'Both',
        };
    }
}
