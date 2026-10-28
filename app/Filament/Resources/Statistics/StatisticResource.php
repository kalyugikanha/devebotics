<?php

namespace App\Filament\Resources\Statistics;

use App\Filament\AdminNavigationGroup;
use App\Filament\Forms\ContentFields;
use App\Filament\Forms\DemoToggle;
use App\Filament\Resources\Statistics\Pages\ManageStatistics;
use App\Filament\Tables\ContentTable;
use App\Models\Statistic;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class StatisticResource extends Resource
{
    protected static ?string $model = Statistic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Company;

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                TextInput::make('label')->required()->maxLength(120)->columnSpanFull()->placeholder('Projects delivered'),
                TextInput::make('prefix')->maxLength(8)->placeholder('$'),
                TextInput::make('value')->required()->numeric()->minValue(0)->maxValue(999999999999),
                TextInput::make('decimals')->numeric()->integer()->minValue(0)->maxValue(2)->default(0),
                TextInput::make('suffix')->maxLength(8)->placeholder('+ or %'),
                TextInput::make('description')->maxLength(180)->columnSpan(2)->helperText('Optional context shown under the number.'),
                ContentFields::icon()->columnSpan(2),
                Toggle::make('is_verified')
                    ->label('Verified figure')
                    ->helperText('Required before this statistic is displayed publicly. Only verify figures you can substantiate.')
                    ->columnSpan(2),
                Toggle::make('is_active')->label('Active')->default(true)->columnSpan(2),
                DemoToggle::make()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('label')->searchable()->weight('medium')->description(fn (Statistic $record): ?string => $record->description),
                TextColumn::make('display')->label('Displayed as')->state(fn (Statistic $record): string => $record->formattedValue())->fontFamily('mono'),
                ToggleColumn::make('is_verified')->label('Verified'),
                ToggleColumn::make('is_active')->label('Active'),
                ContentTable::demo(),
            ])
            ->filters([
                TernaryFilter::make('is_verified')->label('Verified'),
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
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->emptyStateHeading('No statistics yet')
            ->emptyStateDescription('Verified statistics appear as animated counters on the website.')
            ->emptyStateActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStatistics::route('/'),
        ];
    }
}
