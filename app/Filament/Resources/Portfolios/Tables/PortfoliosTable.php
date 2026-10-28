<?php

namespace App\Filament\Resources\Portfolios\Tables;

use App\Enums\ContentStatus;
use App\Filament\Tables\ContentTable;
use App\Models\Portfolio;
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

class PortfoliosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['industry', 'category']))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Portfolio $record): ?string => $record->client_label),
                TextColumn::make('industry.name')->label('Industry')->badge()->color('gray'),
                TextColumn::make('category.name')->label('Category')->toggleable(),
                ContentTable::status(),
                ContentTable::featured(),
                ContentTable::publishedAt(),
                ContentTable::updatedAt(),
                ContentTable::demo(),
            ])
            ->filters([
                ContentTable::statusFilter(ContentStatus::class),
                SelectFilter::make('industry_id')->label('Industry')->relationship('industry', 'name'),
                SelectFilter::make('portfolio_category_id')->label('Category')->relationship('category', 'name'),
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
                    ContentTable::setStatusBulk('publish', 'Publish', ContentStatus::Published, 'portfolio.manage', 'heroicon-o-check-circle', 'success'),
                    ContentTable::setStatusBulk('unpublish', 'Move to draft', ContentStatus::Draft, 'portfolio.manage', 'heroicon-o-pencil-square'),
                    ContentTable::setStatusBulk('archive', 'Archive', ContentStatus::Archived, 'portfolio.manage', 'heroicon-o-archive-box', 'warning'),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-briefcase')
            ->emptyStateHeading('No case studies yet')
            ->emptyStateDescription('Case studies tell the story of real projects: challenge, solution and verified outcomes.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
