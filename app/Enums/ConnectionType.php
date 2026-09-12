<?php

namespace App\Enums;

enum ConnectionType: string
{
    case ProfessionalRequest = 'professional_request';
    case OpportunityApplication = 'opportunity_application';

    public function label(): string
    {
        return match ($this) {
            self::ProfessionalRequest => 'Professional Request',
            self::OpportunityApplication => 'Opportunity Application',
        };
    }
}
