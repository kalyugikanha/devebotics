<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InquiryType: string implements HasLabel
{
    case Quick = 'quick';
    case Project = 'project';

    public function getLabel(): string
    {
        return match ($this) {
            self::Quick => 'Quick inquiry',
            self::Project => 'Project inquiry',
        };
    }
}
