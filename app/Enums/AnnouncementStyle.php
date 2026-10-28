<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AnnouncementStyle: string implements HasLabel
{
    case Brand = 'brand';
    case Neutral = 'neutral';
    case Success = 'success';
    case Warning = 'warning';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
