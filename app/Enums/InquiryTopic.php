<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * What a query is about — chosen by the visitor and used to route it to a team.
 */
enum InquiryTopic: string implements HasColor, HasIcon, HasLabel
{
    case Sales = 'sales';
    case Support = 'support';
    case Partnership = 'partnership';
    case Careers = 'careers';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Sales => 'New project or quote',
            self::Support => 'Support for an existing project',
            self::Partnership => 'Partnership or reseller',
            self::Careers => 'Careers and hiring',
            self::Other => 'Something else',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Sales => 'primary',
            self::Support => 'info',
            self::Partnership => 'success',
            self::Careers => 'warning',
            self::Other => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Sales => 'heroicon-o-rocket-launch',
            self::Support => 'heroicon-o-lifebuoy',
            self::Partnership => 'heroicon-o-hand-raised',
            self::Careers => 'heroicon-o-briefcase',
            self::Other => 'heroicon-o-chat-bubble-left-right',
        };
    }
}
