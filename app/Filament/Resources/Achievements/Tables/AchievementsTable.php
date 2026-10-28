<?php

namespace App\Filament\Resources\Achievements\Tables;

use App\Enums\AchievementType;
use App\Models\Achievement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AchievementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->weight('medium')->description(fn (Achievement $record): ?string => $record->issuer),
                TextColumn::make('type')->badge(),
                IconColumn::make('is_verified')->label('Verified')->boolean(),
                TextColumn::make('expires_on')->label('Expires')->date()->placeholder('—')->color(fn (Achievement $record): string => $record->expires_on?->isPast() ? 'danger' : 'gray'),
                TextColumn::make('visibility')
                    ->label('Public')
                    ->badge()
                    ->state(fn (Achievement $record): string => $record->is_active && $record->is_verified && ($record->expires_on === null || ! $record->expires_on->isPast()) ? 'Shown' : 'Hidden')
                    ->color(fn (string $state): string => $state === 'Shown' ? 'success' : 'gray'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->filters([
                SelectFilter::make('type')->options(AchievementType::class),
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
            ->emptyStateIcon('heroicon-o-check-badge')
            ->emptyStateHeading('No certifications or awards')
            ->emptyStateDescription('Add only credentials the company genuinely holds; the section stays hidden until one is verified.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
