<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ThemeMode: string implements HasLabel
{
    case Light = 'light';
    case Dark = 'dark';
    case System = 'system';

    public function getLabel(): string
    {
        return match ($this) {
            self::Light => 'Light',
            self::Dark => 'Dark',
            self::System => 'Follow system preference',
        };
    }
}
