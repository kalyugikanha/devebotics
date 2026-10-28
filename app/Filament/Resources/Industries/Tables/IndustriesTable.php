<?php

namespace App\Filament\Resources\Industries\Tables;

use App\Enums\ContentStatus;
use App\Filament\Tables\ContentTable;
use App\Models\Industry;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IndustriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['services', 'portfolios']))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Industry $record): ?string => $record->excerpt ? str($record->excerpt)->limit(70)->toString() : null),
                TextColumn::make('services_count')->label('Services')->badge()->color('gray'),
                TextColumn::make('portfolios_count')->label('Case studies')->badge()->color('gray')->toggleable(),
                ContentTable::status(),
                ContentTable::featured(),
                ContentTable::updatedAt(),
                ContentTable::demo(),
            ])
            ->filters([
                ContentTable::statusFilter(ContentStatus::class),
                ContentTable::featuredFilter(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ContentTable::viewOnSite(),
                ContentTable::preview(),
                EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ContentTable::setStatusBulk('publish', 'Publish', ContentStatus::Published, 'industries.manage', 'heroicon-o-check-circle', 'success'),
                    ContentTable::setStatusBulk('unpublish', 'Move to draft', ContentStatus::Draft, 'industries.manage', 'heroicon-o-pencil-square'),
                    ContentTable::setStatusBulk('archive', 'Archive', ContentStatus::Archived, 'industries.manage', 'heroicon-o-archive-box', 'warning'),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-globe-alt')
            ->emptyStateHeading('No industries yet')
            ->emptyStateDescription('Industry pages show relevant services, products and case studies.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
