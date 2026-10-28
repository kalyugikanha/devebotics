<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Enums\ContentStatus;
use App\Filament\Tables\ContentTable;
use App\Models\Page;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('sections'))
            ->defaultSort('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Page $record): string => $record->publicPathForSlug($record->slug)),
                TextColumn::make('template')->badge()->color('gray')->formatStateUsing(fn (string $state): string => Page::TEMPLATES[$state] ?? $state),
                IconColumn::make('is_system')->label('System')->boolean()->trueIcon('heroicon-o-lock-closed')->falseIcon('heroicon-o-minus')->trueColor('warning'),
                TextColumn::make('sections_count')->label('Sections')->badge()->color('gray'),
                ContentTable::status(),
                ContentTable::updatedAt(),
            ])
            ->filters([
                ContentTable::statusFilter(ContentStatus::class),
                SelectFilter::make('template')->options(Page::TEMPLATES),
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
                    ContentTable::setStatusBulk('publish', 'Publish', ContentStatus::Published, 'pages.manage', 'heroicon-o-check-circle', 'success'),
                    ContentTable::setStatusBulk('unpublish', 'Move to draft', ContentStatus::Draft, 'pages.manage', 'heroicon-o-pencil-square'),
                    DeleteBulkAction::make()->authorizeIndividualRecords('delete'),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make()->authorizeIndividualRecords('forceDelete'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('No pages yet')
            ->emptyStateDescription('Create landing pages and documents from reusable sections.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
