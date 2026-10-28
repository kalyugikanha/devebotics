<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PricingModel: string implements HasLabel
{
    case Plans = 'plans';
    case Free = 'free';
    case QuoteOnly = 'quote_only';
    case ContactSales = 'contact_sales';

    public function getLabel(): string
    {
        return match ($this) {
            self::Plans => 'Published pricing plans',
            self::Free => 'Free',
            self::QuoteOnly => 'Request a quote',
            self::ContactSales => 'Contact sales',
        };
    }
}
