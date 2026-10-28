<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Tables\ContentTable;
use App\Models\Client;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['logo', 'industry'])->withCount('portfolios'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('logo_preview')
                    ->label('')
                    ->state(fn (Client $record): ?string => $record->logo?->url())
                    ->imageHeight(28)
                    ->checkFileExistence(false),
                TextColumn::make('name')->searchable()->sortable()->weight('medium')->description(fn (Client $record): ?string => $record->industry?->name),
                IconColumn::make('logo_usage_approved')->label('Logo approved')->boolean(),
                TextColumn::make('portfolios_count')->label('Case studies')->badge()->color('gray'),
                ToggleColumn::make('show_on_home')->label('Homepage'),
                ToggleColumn::make('is_active')->label('Active'),
                ContentTable::demo(),
            ])
            ->filters([
                TernaryFilter::make('logo_usage_approved')->label('Logo approved'),
                TernaryFilter::make('is_active')->label('Active'),
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
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->emptyStateHeading('No clients yet')
            ->emptyStateDescription('Clients with approved logo usage appear in the logo showcase.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
