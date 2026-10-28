<?php

namespace App\Filament\Resources\Industries\Schemas;

use App\Enums\ContentStatus;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\IllustrationSelect;
use App\Filament\Forms\MediaPicker;
use App\Filament\Forms\SeoSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class IndustryForm
{
    public static function configure(Schema $schema): Schema
    {
        $locked = ContentFields::lockedUnless('industries.manage');

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Industry details')
                            ->disabled($locked)
                            ->columns(2)
                            ->schema([
                                ContentFields::title('name', 'Industry name')->columnSpanFull(),
                                ContentFields::slug('industries', '/industries'),
                                ContentFields::icon(),
                                Textarea::make('excerpt')
                                    ->label('Card summary')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->required()
                                    ->helperText('Shown on cards and used as the default meta description.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Detailed description')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([ContentFields::richEditor('description', 'Description')]),

                        Section::make('Challenges & solutions')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Tabs::make('Lists')->tabs([
                                    Tab::make('Challenges')->icon('heroicon-o-exclamation-triangle')->schema([ContentFields::listItems('challenges', 'Typical challenges in this industry')]),
                                    Tab::make('Solutions')->icon('heroicon-o-check-circle')->schema([ContentFields::listItems('solutions', 'How we help')]),
                                ]),
                            ]),

                        Section::make('Relationships')
                            ->disabled($locked)
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Select::make('services')->label('Relevant services')->relationship('services', 'title')->multiple()->searchable()->preload(),
                                Select::make('products')->label('Relevant products')->relationship('products', 'name')->multiple()->searchable()->preload(),
                                Select::make('faqs')->label('FAQs')->relationship('faqs', 'question')->multiple()->searchable()->preload()->columnSpanFull(),
                            ]),

                        SeoSection::make('industries.manage'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        ContentFields::publishing(ContentStatus::class, 'industries.manage'),

                        Section::make('Media')
                            ->disabled($locked)
                            ->schema([MediaPicker::make('hero_media_id', 'heroMedia', 'Hero image'), IllustrationSelect::make()]),

                        Section::make('Record')
                            ->hiddenOn('create')
                            ->schema([
                                TextEntry::make('creator.name')->label('Created by')->placeholder('System'),
                                TextEntry::make('editor.name')->label('Last edited by')->placeholder('System'),
                                TextEntry::make('updated_at')->label('Last updated')->since(),
                            ]),
                    ]),
            ]);
    }
}
