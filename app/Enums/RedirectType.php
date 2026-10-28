<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RedirectType: int implements HasLabel
{
    case Permanent = 301;
    case Temporary = 302;
    case Gone = 410;

    public function getLabel(): string
    {
        return match ($this) {
            self::Permanent => '301 — Moved permanently',
            self::Temporary => '302 — Temporary redirect',
            self::Gone => '410 — Gone (content removed)',
        };
    }
}
