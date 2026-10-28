<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Enums\SectionBackground;
use App\Enums\SectionType;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\LinkInput;
use App\Filament\Forms\MediaPicker;
use App\Models\FaqCategory;
use App\Models\PageSection;
use App\Support\Theme\MotionConfig;
use Closure;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

/**
 * Page builder: ordered, toggleable sections chosen from the SectionType
 * whitelist. Type-specific options are stored in the `data` JSON column.
 */
class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $title = 'Sections';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('type')
                    ->label('Section type')
                    ->options(SectionType::class)
                    ->required()
                    ->live()
                    ->disabledOn('edit')
                    ->helperText(fn (Get $get): ?string => self::type($get)?->getDescription())
                    ->columnSpanFull(),

                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('eyebrow')->label('Eyebrow (small label above the title)')->maxLength(80),
                        TextInput::make('title')->maxLength(180),
                        Textarea::make('subtitle')->rows(2)->maxLength(500)->columnSpanFull(),
                    ]),

                ContentFields::richEditor('content', 'Content')
                    ->visible(self::when(SectionType::RichText, SectionType::MediaText)),

                MediaPicker::make('media_id', 'media', 'Image')
                    ->visible(self::when(SectionType::MediaText))
                    ->columnSpanFull(),

                Fieldset::make('Hero')
                    ->visible(self::when(SectionType::Hero))
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('data.highlights')
                            ->label('Highlight chips')
                            ->placeholder('Add a short highlight and press Enter')
                            ->helperText('Short capability labels shown under the hero text (no unverified claims).')
                            ->columnSpanFull(),
                    ]),

                Fieldset::make('Buttons')
                    ->visible(self::when(SectionType::Hero, SectionType::Cta, SectionType::MediaText))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('data.primary_cta.label')->label('Primary button label')->maxLength(40),
                        LinkInput::make('data.primary_cta.url', 'Primary button link')->requiredWith('data.primary_cta.label'),
                        TextInput::make('data.secondary_cta.label')->label('Secondary button label')->maxLength(40)
                            ->visible(self::when(SectionType::Hero, SectionType::Cta)),
                        LinkInput::make('data.secondary_cta.url', 'Secondary button link')->requiredWith('data.secondary_cta.label')
                            ->visible(self::when(SectionType::Hero, SectionType::Cta)),
                    ]),

                Fieldset::make('Display options')
                    ->visible(fn (Get $get): bool => (bool) self::type($get)?->hasLimit())
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('data.limit')->label('Maximum items')->numeric()->integer()->minValue(1)->maxValue(24)->default(6),
                        Select::make('data.mode')
                            ->label('Which services')
                            ->options(['featured' => 'Featured first', 'all' => 'All in display order'])
                            ->default('featured')
                            ->visible(self::when(SectionType::Services)),
                        Select::make('data.source')
                            ->label('Logos from')
                            ->options(['clients' => 'Clients', 'partners' => 'Partners', 'both' => 'Clients and partners'])
                            ->default('clients')
                            ->visible(self::when(SectionType::Logos)),
                        Select::make('data.style')
                            ->label('Layout')
                            ->options(['marquee' => 'Scrolling marquee', 'grid' => 'Static grid'])
                            ->default('marquee')
                            ->visible(self::when(SectionType::Logos)),
                        Select::make('data.layout')
                            ->label('Layout')
                            ->options(['tabs' => 'Tabs', 'grid' => 'Card grid'])
                            ->default('tabs')
                            ->visible(self::when(SectionType::Industries)),
                        Select::make('data.faq_category')
                            ->label('FAQ source')
                            ->options(fn (): array => FaqCategory::query()->active()->ordered()->pluck('name', 'slug')->all())
                            ->placeholder('FAQs flagged for the homepage')
                            ->visible(self::when(SectionType::Faq)),
                        Toggle::make('data.leadership_only')
                            ->label('Leadership only')
                            ->inline(false)
                            ->visible(self::when(SectionType::Team)),
                    ]),

                Fieldset::make('Mission & vision')
                    ->visible(self::when(SectionType::Values))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('data.mission')->label('Mission')->rows(3)->maxLength(600),
                        Textarea::make('data.vision')->label('Vision')->rows(3)->maxLength(600),
                    ]),

                Repeater::make('data.items')
                    ->label(fn (Get $get): string => self::type($get) === SectionType::Values ? 'Values' : 'Items')
                    ->visible(fn (Get $get): bool => (bool) self::type($get)?->hasItems())
                    ->schema(fn (Get $get): array => array_values(array_filter([
                        TextInput::make('title')->required()->maxLength(120),
                        Textarea::make('description')->rows(2)->maxLength(500),
                        self::type($get) !== SectionType::EngagementModels ? ContentFields::icon() : null,
                        self::type($get) === SectionType::EngagementModels ? TextInput::make('best_for')->label('Best for')->maxLength(120) : null,
                        self::type($get) === SectionType::EngagementModels ? TagsInput::make('features')->placeholder('Add a feature and press Enter') : null,
                        self::type($get) === SectionType::EngagementModels ? Toggle::make('highlighted')->label('Highlight this option') : null,
                    ])))
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                    ->collapsible()
                    ->reorderableWithButtons()
                    ->defaultItems(0)
                    ->maxItems(12)
                    ->addActionLabel('Add item')
                    ->columnSpanFull(),

                Section::make('Appearance')
                    ->collapsible()
                    ->collapsed()
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('background')->options(SectionBackground::class)->default(SectionBackground::Default->value)->required(),
                        Select::make('animation')
                            ->label('Reveal animation')
                            ->options(MotionConfig::REVEAL_PRESETS)
                            ->placeholder('Site default'),
                        TextInput::make('anchor')
                            ->label('Anchor ID')
                            ->prefix('#')
                            ->maxLength(64)
                            ->rule('alpha_dash')
                            ->helperText('Optional, for in-page links.'),
                        Toggle::make('is_enabled')->label('Visible on the website')->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->icon(fn (?SectionType $state): ?string => $state?->getIcon())
                    ->color('gray'),
                TextColumn::make('title')
                    ->placeholder('(no title)')
                    ->description(fn (PageSection $record): ?string => $record->eyebrow)
                    ->wrap(),
                TextColumn::make('background')->badge()->color('gray')->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_enabled')->label('Visible'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add section')
                    ->modalWidth(Width::FourExtraLarge)
                    ->mutateDataUsing(fn (array $data): array => [
                        ...$data,
                        'sort_order' => ((int) $this->getOwnerRecord()->sections()->max('sort_order')) + 1,
                    ]),
            ])
            ->recordActions([
                EditAction::make()->modalWidth(Width::FourExtraLarge),
                ReplicateAction::make()
                    ->label('Duplicate')
                    ->excludeAttributes(['sort_order'])
                    ->beforeReplicaSaved(function (PageSection $replica): void {
                        $replica->sort_order = ((int) $this->getOwnerRecord()->sections()->max('sort_order')) + 1;
                        $replica->is_enabled = false;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No sections yet')
            ->emptyStateDescription('Add sections to build this page. Drag to reorder and toggle visibility at any time.');
    }

    private static function type(Get $get): ?SectionType
    {
        $type = $get('type');

        return $type instanceof SectionType ? $type : SectionType::tryFrom((string) $type);
    }

    private static function when(SectionType ...$types): Closure
    {
        return fn (Get $get): bool => in_array(self::type($get), $types, true);
    }
}
