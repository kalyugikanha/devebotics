<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TeamRole: string implements HasLabel
{
    case Lead = 'lead';
    case Member = 'member';

    public function getLabel(): string
    {
        return match ($this) {
            self::Lead => 'Team lead',
            self::Member => 'Member',
        };
    }
}
