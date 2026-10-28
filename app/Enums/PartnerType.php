<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PartnerType: string implements HasLabel
{
    case Technology = 'technology';
    case Cloud = 'cloud';
    case Strategic = 'strategic';
    case Reseller = 'reseller';
    case Consulting = 'consulting';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Technology => 'Technology partner',
            self::Cloud => 'Cloud partner',
            self::Strategic => 'Strategic alliance',
            self::Reseller => 'Reseller',
            self::Consulting => 'Consulting partner',
            self::Other => 'Other',
        };
    }
}
