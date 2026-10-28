<?php

namespace App\Filament\Resources\Technologies;

use App\Enums\TechnologyCategory;
use App\Filament\AdminNavigationGroup;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\MediaPicker;
use App\Filament\Resources\Technologies\Pages\ManageTechnologies;
use App\Models\Technology;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Catalog;

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                ContentFields::title('name', 'Name'),
                TextInput::make('slug')->label('Slug')->maxLength(180)->rule('alpha_dash')->unique('technologies', 'slug', ignoreRecord: true),
                Select::make('category')->options(TechnologyCategory::class)->default(TechnologyCategory::Other->value)->required(),
                TextInput::make('website_url')->label('Official website')->url()->maxLength(2048),
                MediaPicker::make('logo_media_id', 'logo', 'Logo (optional)')
                    ->helperText('Well-known technologies (Laravel, React, Docker, …) use a bundled logo matched by slug. Upload one only if you have the rights to use it; others show an initials tile.')
                    ->columnSpanFull(),
                Toggle::make('show_on_home')->label('Show in the homepage technology stack'),
                Toggle::make('is_active')->label('Active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('logo')->withCount(['services', 'products', 'portfolios']))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('logo_preview')
                    ->label('')
                    ->state(fn (Technology $record): ?string => $record->logo ? ($record->logo->variantUrl(480) ?? $record->logo->url()) : null)
                    ->imageHeight(28)
                    ->checkFileExistence(false),
                TextColumn::make('name')->searchable()->sortable()->weight('medium'),
                TextColumn::make('category')->badge()->sortable(),
                TextColumn::make('usage')
                    ->label('Used by')
                    ->state(fn (Technology $record): string => ($record->services_count + $record->products_count + $record->portfolios_count).' records')
                    ->color('gray'),
                ToggleColumn::make('show_on_home')->label('Homepage'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->filters([
                SelectFilter::make('category')->options(TechnologyCategory::class),
                TernaryFilter::make('show_on_home')->label('On homepage'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-code-bracket-square')
            ->emptyStateHeading('No technologies yet')
            ->emptyStateDescription('Technologies are tagged on services, products and case studies.')
            ->emptyStateActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTechnologies::route('/'),
        ];
    }
}
