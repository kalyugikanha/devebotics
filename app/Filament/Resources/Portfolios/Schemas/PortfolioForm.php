<?php

namespace App\Filament\Resources\Portfolios\Schemas;

use App\Enums\ContentStatus;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\IllustrationSelect;
use App\Filament\Forms\MediaPicker;
use App\Filament\Forms\SeoSection;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PortfolioForm
{
    public static function configure(Schema $schema): Schema
    {
        $locked = ContentFields::lockedUnless('portfolio.manage');

        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Case study')
                            ->disabled($locked)
                            ->columns(2)
                            ->schema([
                                ContentFields::title()->columnSpanFull(),
                                ContentFields::slug('portfolios', '/case-studies'),
                                Select::make('portfolio_category_id')
                                    ->label('Project category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(120)]),
                                Select::make('industry_id')->label('Industry')->relationship('industry', 'name')->searchable()->preload(),
                                TextInput::make('project_url')->label('Project URL (optional)')->url()->maxLength(2048),
                                Textarea::make('summary')
                                    ->label('Card summary')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->required()
                                    ->helperText('Shown on cards and used as the default meta description.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Client attribution')
                            ->description('Only name a client publicly when they have approved it. Otherwise keep the case study anonymised and use a descriptive label.')
                            ->disabled($locked)
                            ->columns(2)
                            ->schema([
                                Select::make('client_id')->label('Client record (internal)')->relationship('client', 'name')->searchable()->preload(),
                                TextInput::make('client_label')
                                    ->label('Public client label')
                                    ->maxLength(180)
                                    ->required()
                                    ->placeholder('A regional healthcare provider'),
                                Toggle::make('is_client_anonymized')
                                    ->label('Anonymise client')
                                    ->default(true)
                                    ->helperText('When off, the client name is shown only if the client has an approved logo/usage record.')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Story')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Tabs::make('Story')->tabs([
                                    Tab::make('Challenge')->schema([ContentFields::richEditor('challenge', 'Challenge')]),
                                    Tab::make('Solution')->schema([ContentFields::richEditor('solution', 'Solution')]),
                                    Tab::make('Outcome')->schema([ContentFields::richEditor('outcome', 'Outcome')]),
                                ]),
                            ]),

                        Section::make('Results & metrics')
                            ->description('Only metrics marked as verified are shown on the website. Never publish figures that cannot be substantiated.')
                            ->disabled($locked)
                            ->collapsible()
                            ->schema([
                                Repeater::make('metrics')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextInput::make('value')->required()->maxLength(40)->placeholder('40%'),
                                        TextInput::make('label')->required()->maxLength(120)->placeholder('Faster processing time'),
                                        Toggle::make('is_verified')->label('Verified')->inline(false),
                                    ])
                                    ->columns(3)
                                    ->itemLabel(fn (array $state): ?string => trim(($state['value'] ?? '').' '.($state['label'] ?? '')) ?: null)
                                    ->reorderableWithButtons()
                                    ->defaultItems(0)
                                    ->maxItems(8)
                                    ->addActionLabel('Add metric'),
                            ]),

                        Section::make('Timeline')
                            ->disabled($locked)
                            ->collapsible()
                            ->collapsed()
                            ->columns(3)
                            ->schema([
                                TextInput::make('duration')->maxLength(60)->placeholder('6 months'),
                                DatePicker::make('started_on')->native(false),
                                DatePicker::make('completed_on')->native(false)->afterOrEqual('started_on'),
                                Toggle::make('show_timeline')->label('Show dates publicly (only if approved by the client)')->columnSpanFull(),
                            ]),

                        Section::make('Relationships')
                            ->disabled($locked)
                            ->collapsible()
                            ->columns(2)
                            ->schema([
                                Select::make('services')->label('Services delivered')->relationship('services', 'title')->multiple()->searchable()->preload(),
                                Select::make('products')->label('Products used')->relationship('products', 'name')->multiple()->searchable()->preload(),
                                Select::make('technologies')->relationship('technologies', 'name')->multiple()->searchable()->preload()->columnSpanFull()
                                    ->createOptionForm([TextInput::make('name')->required()->maxLength(80)]),
                            ]),

                        Section::make('Gallery')
                            ->disabled($locked)
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Repeater::make('galleryItems')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->schema([
                                        MediaPicker::make('media_id', 'media', 'Image')->required(),
                                        TextInput::make('caption')->maxLength(200),
                                    ])
                                    ->columns(2)
                                    ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => [...$data, 'collection' => 'gallery'])
                                    ->defaultItems(0)
                                    ->maxItems(20)
                                    ->collapsible()
                                    ->addActionLabel('Add image'),
                            ]),

                        SeoSection::make('portfolio.manage'),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        ContentFields::publishing(ContentStatus::class, 'portfolio.manage'),

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
