<?php

namespace App\Filament\Pages\Settings;

use App\Filament\AdminNavigationGroup;
use App\Filament\Forms\MediaIdSelect;
use App\Support\Seo\SeoData;
use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use UnitEnum;

class SeoSettings extends SettingsPage
{
    protected static string $permission = 'seo.manage';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Seo;

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'SEO settings';

    protected static ?string $title = 'SEO settings';

    protected static ?string $slug = 'settings/seo';

    protected static function settingsGroups(): array
    {
        return ['seo'];
    }

    protected function formComponents(): array
    {
        $token = ['regex:/^[A-Za-z0-9_\-]{10,100}$/'];

        return [
            Callout::make(fn (): string => SeoData::siteAllowsIndexing() ? 'Search engines may index this site' : 'Search engines are currently blocked')
                ->description(fn (): string => 'Indexing requires APP_ENV=production ('.(app()->isProduction() ? 'yes' : 'currently '.app()->environment()).'), APP_ALLOW_INDEXING=true ('.(config('softec.allow_indexing') ? 'yes' : 'no').') and the toggle below. Staging and local environments always send noindex.')
                ->color(fn (): string => SeoData::siteAllowsIndexing() ? 'success' : 'warning')
                ->icon('heroicon-o-globe-alt'),
            Section::make('Defaults')->schema([
                TextInput::make('seo.title_suffix')->label('Title suffix')->maxLength(60)->helperText('Appended to page titles, e.g. “ | Softec Ai Solution”.'),
                Textarea::make('seo.default_description')
                    ->label('Default meta description')
                    ->rows(3)
                    ->maxLength(320)
                    ->live(debounce: 400)
                    ->hint(fn (?string $state): string => Str::length((string) $state).' / 155 recommended')
                    ->hintColor(fn (?string $state): string => Str::length((string) $state) > 160 ? 'warning' : 'gray'),
                MediaIdSelect::make('seo.default_og_image_media_id', 'Default social sharing image (1200×630)', category: 'brand'),
                TextInput::make('seo.x_handle')->label('X (Twitter) handle')->prefix('@')->maxLength(30)->regex('/^[A-Za-z0-9_]{1,15}$/'),
            ]),
            Section::make('Indexing & robots')->columns(2)->schema([
                Toggle::make('seo.allow_indexing')->label('Allow search engines to index the website'),
                Toggle::make('seo.local_business_enabled')->label('Output LocalBusiness structured data')
                    ->helperText('Only enable when a real, public business address is configured under Company & contact.'),
                TagsInput::make('seo.robots_disallow')
                    ->label('Additional robots.txt disallow paths')
                    ->placeholder('/private-path')
                    ->nestedRecursiveRules(['regex:/^\/[A-Za-z0-9_\-\/\.\*]*$/', 'max:200'])
                    ->columnSpanFull(),
            ]),
            Section::make('Search engine verification')->columns(2)->schema([
                TextInput::make('seo.google_site_verification')->label('Google Search Console token')->rules($token)
                    ->helperText('The content value of the google-site-verification meta tag.'),
                TextInput::make('seo.bing_site_verification')->label('Bing Webmaster token')->rules($token),
            ]),
        ];
    }

    protected function mutateGroupBeforeSave(string $group, array $values): array
    {
        $values = parent::mutateGroupBeforeSave($group, $values);
        $values['robots_disallow'] = array_values(array_unique(array_filter((array) ($values['robots_disallow'] ?? []))));
        $values['allow_indexing'] = (bool) ($values['allow_indexing'] ?? false);
        $values['local_business_enabled'] = (bool) ($values['local_business_enabled'] ?? false);

        return $values;
    }

    protected function afterSave(): void
    {
        foreach (['pages', 'services', 'products', 'industries', 'case-studies', 'blog', 'careers'] as $type) {
            cache()->forget('sitemap.'.$type);
        }
    }
}
