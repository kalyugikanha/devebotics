<?php

namespace App\Filament\Resources\Media\Tables;

use App\Filament\Resources\Media\Actions\MediaDeletion;
use App\Models\Media;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('uploader'))
            ->defaultSort('created_at', 'desc')
            ->contentGrid(['md' => 2, 'xl' => 4])
            ->paginationPageOptions([24, 48, 96])
            ->columns([
                Stack::make([
                    ImageColumn::make('thumbnail')
                        ->label('')
                        ->state(fn (Media $record): ?string => $record->isImage() ? ($record->variantUrl(480) ?? $record->url()) : null)
                        ->defaultImageUrl('data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="320" height="180"><rect width="320" height="180" fill="#f1f5f9"/><text x="160" y="100" text-anchor="middle" font-family="Arial" font-size="22" font-weight="700" fill="#64748b">PDF</text></svg>'))
                        ->imageHeight(150)
                        ->imageWidth('100%')
                        ->extraImgAttributes(['class' => 'object-cover rounded-lg w-full', 'loading' => 'lazy'])
                        ->checkFileExistence(false),
                    TextColumn::make('title')
                        ->searchable(['title', 'original_name', 'alt_text'])
                        ->sortable()
                        ->weight('medium')
                        ->limit(40),
                    Split::make([
                        TextColumn::make('extension')->badge()->color('gray')->formatStateUsing(fn (string $state): string => strtoupper($state))->grow(false),
                        TextColumn::make('size')->formatStateUsing(fn (Media $record): string => $record->humanSize())->color('gray')->size('xs')->sortable(),
                        TextColumn::make('dimensions')
                            ->state(fn (Media $record): ?string => $record->width ? $record->width.'×'.$record->height : null)
                            ->color('gray')
                            ->size('xs'),
                    ]),
                    TextColumn::make('alt_status')
                        ->state(fn (Media $record): ?string => $record->isImage() && blank($record->alt_text) ? 'Missing alt text' : null)
                        ->badge()
                        ->color('warning')
                        ->icon('heroicon-m-exclamation-triangle'),
                    TextColumn::make('category')->badge()->color('primary')->size('xs'),
                ])->space(2),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(['image' => 'Images', 'pdf' => 'PDF documents', 'svg' => 'SVG graphics'])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'image' => $query->where('mime_type', 'like', 'image/%'),
                        'pdf' => $query->where('mime_type', 'application/pdf'),
                        'svg' => $query->where('mime_type', 'image/svg+xml'),
                        default => $query,
                    }),
                SelectFilter::make('category')
                    ->options(fn (): array => Media::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category', 'category')->all()),
                Filter::make('missing_alt')
                    ->label('Images missing alt text')
                    ->query(fn (Builder $query): Builder => $query->where('mime_type', 'like', 'image/%')->where(fn (Builder $q) => $q->whereNull('alt_text')->orWhere('alt_text', ''))),
            ])
            ->recordActions([
                EditAction::make(),
                MediaDeletion::action(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    MediaDeletion::bulkAction(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-photo')
            ->emptyStateHeading('The media library is empty')
            ->emptyStateDescription('Upload images and documents to reuse them across pages, services, products and posts.')
            ->emptyStateActions([CreateAction::make()->label('Upload file')]);
    }
}
