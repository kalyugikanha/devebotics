<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TechnologyCategory: string implements HasLabel
{
    case Frontend = 'frontend';
    case Backend = 'backend';
    case Mobile = 'mobile';
    case AiMl = 'ai_ml';
    case Cloud = 'cloud';
    case Database = 'database';
    case DevOps = 'devops';
    case Design = 'design';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Frontend => 'Frontend',
            self::Backend => 'Backend',
            self::Mobile => 'Mobile',
            self::AiMl => 'AI & Machine Learning',
            self::Cloud => 'Cloud',
            self::Database => 'Data & Databases',
            self::DevOps => 'DevOps',
            self::Design => 'Design',
            self::Other => 'Other',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Frontend => 'window',
            self::Backend => 'server-stack',
            self::Mobile => 'device-phone-mobile',
            self::AiMl => 'cpu-chip',
            self::Cloud => 'cloud',
            self::Database => 'circle-stack',
            self::DevOps => 'cog-6-tooth',
            self::Design => 'paint-brush',
            self::Other => 'puzzle-piece',
        };
    }
}
