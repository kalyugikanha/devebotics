<?php

namespace App\Filament\Resources\Partners\Tables;

use App\Enums\PartnerType;
use App\Filament\Tables\ContentTable;
use App\Models\Partner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('logo'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('logo_preview')
                    ->label('')
                    ->state(fn (Partner $record): ?string => $record->logo?->url())
                    ->imageHeight(28)
                    ->checkFileExistence(false),
                TextColumn::make('name')->searchable()->sortable()->weight('medium'),
                TextColumn::make('type')->badge(),
                IconColumn::make('logo_usage_approved')->label('Logo approved')->boolean(),
                ToggleColumn::make('is_active')->label('Active'),
                ContentTable::demo(),
            ])
            ->filters([
                SelectFilter::make('type')->options(PartnerType::class),
                TernaryFilter::make('logo_usage_approved')->label('Logo approved'),
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
            ->emptyStateIcon('heroicon-o-hand-raised')
            ->emptyStateHeading('No partners yet')
            ->emptyStateDescription('Partners with approved logo usage appear in the partners & alliances section.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
