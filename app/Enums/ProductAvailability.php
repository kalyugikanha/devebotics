<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProductAvailability: string implements HasColor, HasLabel
{
    case Available = 'available';
    case Beta = 'beta';
    case ComingSoon = 'coming_soon';
    case CustomBuild = 'custom_build';
    case Discontinued = 'discontinued';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Beta => 'Beta',
            self::ComingSoon => 'Coming soon',
            self::CustomBuild => 'Built to order',
            self::Discontinued => 'Discontinued',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Beta => 'info',
            self::ComingSoon => 'warning',
            self::CustomBuild => 'primary',
            self::Discontinued => 'gray',
        };
    }

    /**
     * Schema.org availability URL, only for statuses that map cleanly.
     */
    public function schemaOrgAvailability(): ?string
    {
        return match ($this) {
            self::Available => 'https://schema.org/InStock',
            self::ComingSoon => 'https://schema.org/PreOrder',
            self::Discontinued => 'https://schema.org/Discontinued',
            default => null,
        };
    }
}
