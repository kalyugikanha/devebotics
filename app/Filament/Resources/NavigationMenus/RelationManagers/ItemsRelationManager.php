<?php

namespace App\Filament\Resources\NavigationMenus\RelationManagers;

use App\Filament\Forms\ContentFields;
use App\Filament\Forms\LinkInput;
use App\Models\Industry;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\Product;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'allItems';

    protected static ?string $title = 'Menu items';

    protected static ?string $recordTitleAttribute = 'label';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('label')->required()->maxLength(80),
                Select::make('parent_id')
                    ->label('Parent item')
                    ->placeholder('Top level')
                    ->options(fn (?NavigationItem $record): array => $this->getOwnerRecord()->allItems()
                        ->whereNull('parent_id')
                        ->where('link_type', '!=', 'services_mega')
                        ->when($record, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
                        ->pluck('label', 'id')
                        ->all())
                    ->disabled(fn (?NavigationItem $record): bool => (bool) $record?->children()->exists())
                    ->helperText(fn (?NavigationItem $record): ?string => $record?->children()->exists() ? 'This item has children, so it must stay at the top level.' : 'Menus support two levels.'),
                ToggleButtons::make('link_type')
                    ->label('Links to')
                    ->options(NavigationItem::LINK_TYPES)
                    ->default('route')
                    ->required()
                    ->live()
                    ->columnSpanFull(),
                Select::make('route_name')
                    ->label('Website section')
                    ->options(NavigationItem::ROUTES)
                    ->required()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'route'),
                LinkInput::make('url', 'URL', httpsOnly: true)
                    ->required()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'url'),
                MorphToSelect::make('linkable')
                    ->label('Target')
                    ->types([
                        Type::make(Page::class)->titleAttribute('title')->modifyOptionsQueryUsing(fn (Builder $query) => $query->published()),
                        Type::make(Service::class)->titleAttribute('title')->modifyOptionsQueryUsing(fn (Builder $query) => $query->published()),
                        Type::make(Product::class)->titleAttribute('name')->modifyOptionsQueryUsing(fn (Builder $query) => $query->published()),
                        Type::make(Industry::class)->titleAttribute('name')->modifyOptionsQueryUsing(fn (Builder $query) => $query->published()),
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn (Get $get): bool => $get('link_type') === 'linkable')
                    ->columnSpanFull(),
                TextInput::make('description')
                    ->maxLength(160)
                    ->helperText('Optional, shown under the label in dropdown menus.')
                    ->columnSpanFull(),
                Grid::make(4)
                    ->columnSpanFull()
                    ->schema([
                        ContentFields::icon()->columnSpan(1),
                        Toggle::make('open_in_new_tab')->label('Open in new tab')->inline(false),
                        Toggle::make('is_highlighted')->label('Highlight as button')->inline(false),
                        Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['parent', 'linkable']))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('label')
                    ->weight('medium')
                    ->formatStateUsing(fn (NavigationItem $record, string $state): string => $record->parent_id ? '↳ '.$state : $state)
                    ->description(fn (NavigationItem $record): ?string => $record->parent ? 'Under: '.$record->parent->label : null),
                TextColumn::make('link_type')->label('Type')->badge()->color('gray')->formatStateUsing(fn (string $state): string => NavigationItem::LINK_TYPES[$state] ?? $state),
                TextColumn::make('target')
                    ->label('Destination')
                    ->state(fn (NavigationItem $record): string => $record->resolveUrl() ?? 'Unavailable (unpublished or missing)')
                    ->color(fn (NavigationItem $record): string => $record->resolveUrl() === null ? 'danger' : 'gray')
                    ->limit(50),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->filters([
                TernaryFilter::make('parent_id')->label('Level')->nullable()->trueLabel('Child items')->falseLabel('Top-level items'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add item')
                    ->mutateDataUsing(fn (array $data): array => self::normalize($data) + [
                        'sort_order' => ((int) $this->getOwnerRecord()->allItems()->max('sort_order')) + 1,
                    ]),
            ])
            ->recordActions([
                EditAction::make()->mutateDataUsing(fn (array $data): array => self::normalize($data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No menu items')
            ->emptyStateDescription('Add links to website sections, pages, services or custom URLs.');
    }

    /**
     * Clear fields that do not belong to the chosen link type.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalize(array $data): array
    {
        $type = $data['link_type'] ?? 'route';

        if ($type !== 'route') {
            $data['route_name'] = null;
        }

        if ($type !== 'url') {
            $data['url'] = null;
        }

        if ($type !== 'linkable') {
            $data['linkable_type'] = null;
            $data['linkable_id'] = null;
        }

        if ($type === 'services_mega') {
            $data['parent_id'] = null;
        }

        return $data;
    }
}
