<?php

namespace App\Filament\Resources\NavigationMenus;

use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\NavigationMenus\Pages\CreateNavigationMenu;
use App\Filament\Resources\NavigationMenus\Pages\EditNavigationMenu;
use App\Filament\Resources\NavigationMenus\Pages\ListNavigationMenus;
use App\Filament\Resources\NavigationMenus\RelationManagers\ItemsRelationManager;
use App\Models\NavigationMenu;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class NavigationMenuResource extends Resource
{
    protected static ?string $model = NavigationMenu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Website;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Navigation menus';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')->required()->maxLength(120),
                        Select::make('location')
                            ->options(NavigationMenu::LOCATIONS)
                            ->required()
                            ->unique('navigation_menus', 'location', ignoreRecord: true)
                            ->helperText('Each location can hold one menu.'),
                        TextInput::make('title')->label('Heading (footer columns)')->maxLength(80),
                        Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('allItems'))
            ->columns([
                TextColumn::make('name')->searchable()->weight('medium')->description(fn (NavigationMenu $record): ?string => $record->title),
                TextColumn::make('location')->badge()->color('gray')->formatStateUsing(fn (string $state): string => NavigationMenu::LOCATIONS[$state] ?? $state),
                TextColumn::make('all_items_count')->label('Items')->badge(),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-bars-3')
            ->emptyStateHeading('No menus yet')
            ->emptyStateActions([CreateAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNavigationMenus::route('/'),
            'create' => CreateNavigationMenu::route('/create'),
            'edit' => EditNavigationMenu::route('/{record}/edit'),
        ];
    }
}
