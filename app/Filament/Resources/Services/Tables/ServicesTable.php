<?php

namespace App\Filament\Resources\Services\Tables;

use App\Enums\ContentStatus;
use App\Filament\Tables\ContentTable;
use App\Models\Service;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('category'))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Service $record): ?string => $record->category?->name),
                ContentTable::status(),
                ContentTable::featured(),
                ContentTable::publishedAt(),
                ContentTable::updatedAt(),
                ContentTable::demo(),
            ])
            ->filters([
                ContentTable::statusFilter(ContentStatus::class),
                SelectFilter::make('service_category_id')->label('Category')->relationship('category', 'name'),
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
                    ContentTable::setStatusBulk('publish', 'Publish', ContentStatus::Published, 'services.manage', 'heroicon-o-check-circle', 'success'),
                    ContentTable::setStatusBulk('unpublish', 'Move to draft', ContentStatus::Draft, 'services.manage', 'heroicon-o-pencil-square'),
                    ContentTable::setStatusBulk('archive', 'Archive', ContentStatus::Archived, 'services.manage', 'heroicon-o-archive-box', 'warning'),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-cpu-chip')
            ->emptyStateHeading('No services yet')
            ->emptyStateDescription('Services you add here appear on the website once published.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
