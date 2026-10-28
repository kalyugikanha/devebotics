<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SectionBackground: string implements HasLabel
{
    case Default = 'default';
    case Muted = 'muted';
    case Gradient = 'gradient';
    case Contrast = 'contrast';

    public function getLabel(): string
    {
        return match ($this) {
            self::Default => 'Default surface',
            self::Muted => 'Muted surface',
            self::Gradient => 'Brand gradient wash',
            self::Contrast => 'High-contrast (dark) band',
        };
    }
}
