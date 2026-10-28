<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum WorkplaceType: string implements HasLabel
{
    case OnSite = 'on_site';
    case Hybrid = 'hybrid';
    case Remote = 'remote';

    public function getLabel(): string
    {
        return match ($this) {
            self::OnSite => 'On-site',
            self::Hybrid => 'Hybrid',
            self::Remote => 'Remote',
        };
    }
}
