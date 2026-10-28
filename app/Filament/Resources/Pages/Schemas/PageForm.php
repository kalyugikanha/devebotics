<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\ContentStatus;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\MediaPicker;
use App\Filament\Forms\SeoSection;
use App\Models\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        $locked = ContentFields::lockedUnless('pages.manage');
        $isSystem = fn (?Page $record): bool => (bool) $record?->is_system;

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Page')
                            ->disabled($locked)
                            ->columns(2)
                            ->schema([
                                ContentFields::title()->columnSpanFull(),
                                ContentFields::slug('pages', '/')
                                    ->disabled($isSystem)
                                    ->rule(fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                                        if (in_array($value, (array) config('softec.reserved_slugs'), true)) {
                                            $fail('This URL is reserved by the website. Please choose another slug.');
                                        }
                                    })
                                    ->helperText(fn (?Page $record): string => $record?->is_system
                                        ? 'System page — the URL is fixed because the website links to it.'
                                        : 'Lowercase letters, numbers and dashes. Changing the slug of a published page creates a 301 redirect automatically.'),
                                Select::make('template')
                                    ->options(Page::TEMPLATES)
                                    ->default('default')
                                    ->required()
                                    ->live()
                                    ->disabled($isSystem)
                                    ->helperText('Section-based templates are built from the sections below; legal pages render the document body with a table of contents.'),
                                Textarea::make('excerpt')
                                    ->label('Introduction')
                                    ->rows(2)
                                    ->maxLength(400)
                                    ->helperText('Shown under the page title and used as the default meta description.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Page body')
                            ->disabled($locked)
                            ->collapsible()
                            ->visible(fn (Get $get): bool => in_array($get('template'), ['default', 'legal'], true))
                            ->schema([ContentFields::richEditor('body', 'Body')]),

                        SeoSection::make('pages.manage'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        ContentFields::publishing(ContentStatus::class, 'pages.manage', featured: false),

                        Section::make('Options')
                            ->disabled($locked)
                            ->schema([
                                MediaPicker::make('hero_media_id', 'heroMedia', 'Hero image (optional)'),
                                Toggle::make('show_in_sitemap')->label('Include in XML sitemap')->default(true),
                            ]),

                        Section::make('Record')
                            ->hiddenOn('create')
                            ->schema([
                                TextEntry::make('is_system')
                                    ->label('Type')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'System page (cannot be deleted)' : 'Custom page')
                                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                                TextEntry::make('creator.name')->label('Created by')->placeholder('System'),
                                TextEntry::make('editor.name')->label('Last edited by')->placeholder('System'),
                                TextEntry::make('updated_at')->label('Last updated')->since(),
                            ]),
                    ]),
            ]);
    }
}
