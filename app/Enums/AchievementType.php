<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AchievementType: string implements HasLabel
{
    case Certification = 'certification';
    case Award = 'award';
    case Membership = 'membership';
    case Achievement = 'achievement';

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
