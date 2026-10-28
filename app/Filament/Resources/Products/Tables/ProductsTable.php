<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ContentStatus;
use App\Enums\ProductAvailability;
use App\Filament\Tables\ContentTable;
use App\Models\Product;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['category', 'logo']))
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('logo_preview')
                    ->label('')
                    ->state(fn (Product $record): ?string => $record->logo ? ($record->logo->variantUrl(480) ?? $record->logo->url()) : null)
                    ->imageSize(36)
                    ->checkFileExistence(false),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Product $record): ?string => $record->category?->name),
                TextColumn::make('availability')->badge()->sortable(),
                TextColumn::make('pricing_model')->label('Pricing')->badge()->color('gray')->toggleable(),
                ContentTable::status(),
                ContentTable::featured(),
                ContentTable::publishedAt(),
                ContentTable::updatedAt(),
                ContentTable::demo(),
            ])
            ->filters([
                ContentTable::statusFilter(ContentStatus::class),
                SelectFilter::make('product_category_id')->label('Category')->relationship('category', 'name'),
                SelectFilter::make('availability')->options(ProductAvailability::class),
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
                    ContentTable::setStatusBulk('publish', 'Publish', ContentStatus::Published, 'products.manage', 'heroicon-o-check-circle', 'success'),
                    ContentTable::setStatusBulk('unpublish', 'Move to draft', ContentStatus::Draft, 'products.manage', 'heroicon-o-pencil-square'),
                    ContentTable::setStatusBulk('archive', 'Archive', ContentStatus::Archived, 'products.manage', 'heroicon-o-archive-box', 'warning'),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-cube')
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Products appear in the product directory once published.')
            ->emptyStateActions([CreateAction::make()]);
    }
}
