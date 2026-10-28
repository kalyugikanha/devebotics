<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\AdminNavigationGroup;
use App\Filament\Forms\DemoToggle;
use App\Filament\Resources\Milestones\Pages\ManageMilestones;
use App\Filament\Tables\ContentTable;
use App\Models\Milestone;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Company;

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('year')->label('Year / period')->required()->maxLength(16)->placeholder('2024'),
                TextInput::make('title')->required()->maxLength(180)->columnSpan(2),
                Textarea::make('description')->rows(3)->maxLength(600)->columnSpanFull(),
                Toggle::make('is_active')->label('Active')->default(true),
                DemoToggle::make()->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('year')->badge()->color('gray'),
                TextColumn::make('title')->searchable()->weight('medium')->description(fn (Milestone $record): ?string => $record->description ? str($record->description)->limit(80)->toString() : null),
                ToggleColumn::make('is_active')->label('Active'),
                ContentTable::demo(),
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
            ->emptyStateIcon('heroicon-o-flag')
            ->emptyStateHeading('No milestones yet')
            ->emptyStateDescription('Milestones build the company story timeline on the About page.')
            ->emptyStateActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMilestones::route('/'),
        ];
    }
}
