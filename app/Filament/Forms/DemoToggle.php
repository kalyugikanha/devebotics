<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;

/**
 * "Demo content" flag, only shown while a record is still flagged as demo.
 */
final class DemoToggle
{
    public static function make(): Toggle
    {
        return Toggle::make('is_demo')
            ->label('Demo content')
            ->helperText('Seeded demonstration content is labelled publicly. Untick once replaced with real, verified content.')
            ->visible(fn (Get $get): bool => (bool) $get('is_demo'));
    }
}
