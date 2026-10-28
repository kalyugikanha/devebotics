<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Filament\Forms\MediaPicker;
use App\Models\Media;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Upload')
                            ->visibleOn('create')
                            ->schema([
                                ...MediaPicker::uploadSchema(imagesOnly: false),
                                self::categoryInput(),
                                Textarea::make('caption')->rows(2)->maxLength(500),
                            ]),

                        Section::make('Details')
                            ->hiddenOn('create')
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')->maxLength(150)->required(),
                                self::categoryInput(),
                                TextInput::make('alt_text')
                                    ->label('Alternative text')
                                    ->maxLength(250)
                                    ->helperText('Describe what the image shows for screen-reader users. Avoid keyword stuffing; leave empty only for decorative images.')
                                    ->columnSpanFull(),
                                Textarea::make('caption')->rows(2)->maxLength(500)->columnSpanFull(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->hiddenOn('create')
                    ->schema([
                        Section::make('Preview')
                            ->schema([
                                ImageEntry::make('preview')
                                    ->hiddenLabel()
                                    ->state(fn (?Media $record): ?string => $record?->isImage() ? ($record->variantUrl(960) ?? $record->url()) : null)
                                    ->visible(fn (?Media $record): bool => (bool) $record?->isImage())
                                    ->imageWidth('100%')
                                    ->imageHeight('auto')
                                    ->checkFileExistence(false),
                                TextEntry::make('public_url')
                                    ->label('Public URL')
                                    ->state(fn (?Media $record): ?string => $record?->url())
                                    ->copyable()
                                    ->copyMessage('URL copied')
                                    ->limit(48),
                            ]),
                        Section::make('File information')
                            ->schema([
                                TextEntry::make('original_name')->label('Original file name'),
                                TextEntry::make('mime_type')->label('Type'),
                                TextEntry::make('size')->label('Size')->formatStateUsing(fn (?Media $record): string => $record?->humanSize() ?? '—'),
                                TextEntry::make('dimensions')
                                    ->label('Dimensions')
                                    ->state(fn (?Media $record): string => $record?->width ? $record->width.' × '.$record->height.' px' : '—'),
                                TextEntry::make('variants')
                                    ->label('Responsive variants')
                                    ->state(fn (?Media $record): string => collect($record?->variants['webp'] ?? [])->keys()->map(fn ($width) => $width.'w')->implode(', ') ?: 'None'),
                                TextEntry::make('uploader.name')->label('Uploaded by')->placeholder('System'),
                                TextEntry::make('created_at')->label('Uploaded')->dateTime(),
                            ]),
                    ]),
            ]);
    }

    private static function categoryInput(): TextInput
    {
        return TextInput::make('category')
            ->maxLength(64)
            ->datalist(fn (): array => Media::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')->all())
            ->helperText('Group files, e.g. "logos", "team", "blog".');
    }
}
