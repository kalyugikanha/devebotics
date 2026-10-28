<?php

namespace App\Filament\Forms;

use App\Support\Art\IllustrationRegistry;
use Filament\Forms\Components\Select;

/**
 * Choose one of the built-in illustrations shown when no hero image is uploaded.
 * Only whitelisted names are accepted; empty means "pick automatically".
 */
final class IllustrationSelect
{
    public static function make(): Select
    {
        return Select::make('illustration')
            ->label('Illustration')
            ->options(IllustrationRegistry::all())
            ->in(array_keys(IllustrationRegistry::all()))
            ->searchable()
            ->placeholder('Automatic (matched to this item)')
            ->helperText('Original artwork used on cards and page headers until a hero image is uploaded.');
    }
}
