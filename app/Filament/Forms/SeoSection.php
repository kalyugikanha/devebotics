<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Reusable SEO metadata section stored in the polymorphic `seo_meta` table.
 * Editable by users holding the module's manage permission or `seo.manage`.
 */
final class SeoSection
{
    public static function make(string $modulePermission): Section
    {
        return Section::make('Search engine optimisation')
            ->description('Optional overrides. Sensible defaults are generated from the content when left empty.')
            ->icon('heroicon-o-magnifying-glass')
            ->relationship('seo')
            ->collapsible()
            ->collapsed()
            ->disabled(fn (): bool => ! Auth::user()?->canAny([$modulePermission, 'seo.manage']))
            ->schema([
                TextInput::make('meta_title')
                    ->label('Meta title')
                    ->maxLength(70)
                    ->live(debounce: 400)
                    ->hint(fn (?string $state): string => Str::length((string) $state).' / 60 recommended')
                    ->hintColor(fn (?string $state): string => Str::length((string) $state) > 60 ? 'warning' : 'gray')
                    ->helperText('The site name suffix is appended automatically.'),
                Textarea::make('meta_description')
                    ->label('Meta description')
                    ->rows(3)
                    ->maxLength(320)
                    ->live(debounce: 400)
                    ->hint(fn (?string $state): string => Str::length((string) $state).' / 155 recommended')
                    ->hintColor(fn (?string $state): string => Str::length((string) $state) > 160 ? 'warning' : 'gray'),
                Grid::make(2)->schema([
                    TextInput::make('focus_keyword')
                        ->label('Focus keyword / topic')
                        ->maxLength(120)
                        ->helperText('Editorial guidance only — not output as a meta tag.'),
                    TextInput::make('canonical_url')
                        ->label('Canonical URL')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('Only set when this content duplicates another URL.'),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('og_title')->label('Social share title')->maxLength(95),
                    MediaPicker::make('og_image_id', 'ogImage', 'Social share image (1200×630 recommended)'),
                ]),
                Textarea::make('og_description')->label('Social share description')->rows(2)->maxLength(200),
                Grid::make(2)->schema([
                    Toggle::make('robots_index')->label('Allow search engines to index')->default(true),
                    Toggle::make('robots_follow')->label('Allow search engines to follow links')->default(true),
                ]),
                TextInput::make('keywords')
                    ->label('Related topics')
                    ->maxLength(500)
                    ->helperText('Comma-separated topics for internal reference and linking suggestions. Avoid keyword stuffing.'),
            ]);
    }
}
