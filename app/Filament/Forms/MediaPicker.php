<?php

namespace App\Filament\Forms;

use App\Models\Media;
use App\Support\Media\MediaLibrary;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Select a file from the central media library (with thumbnails), or upload
 * a new one inline. Uploads go through MediaLibrary for MIME detection,
 * SVG sanitising, metadata stripping and responsive variants.
 */
final class MediaPicker
{
    public static function make(string $field, string $relationship, string $label, bool $imagesOnly = true, ?string $category = null): Select
    {
        return Select::make($field)
            ->label($label)
            ->relationship(
                name: $relationship,
                titleAttribute: 'title',
                modifyQueryUsing: fn (Builder $query) => $imagesOnly ? $query->where('mime_type', 'like', 'image/%')->latest('id') : $query->latest('id'),
            )
            ->getOptionLabelFromRecordUsing(fn (Media $record): string => self::optionLabel($record))
            ->allowHtml()
            ->searchable(['title', 'original_name', 'alt_text'])
            ->preload()
            ->optionsLimit(40)
            ->createOptionForm(self::uploadSchema($imagesOnly, $category))
            ->createOptionUsing(function (array $data) use ($category): int {
                return app(MediaLibrary::class)->importFromDisk('local', $data['upload'], [
                    'alt_text' => $data['alt_text'] ?? null,
                    'title' => $data['title'] ?? null,
                    'category' => $category,
                ], $data['upload_name'] ?? null)->getKey();
            })
            ->createOptionAction(fn (Action $action) => $action
                ->modalHeading('Upload to media library')
                ->visible(fn (): bool => (bool) Auth::user()?->can('media.manage')))
            ->helperText($imagesOnly ? 'Choose from the media library or upload a new image. Remember descriptive alt text.' : null);
    }

    /**
     * @return array<int, mixed>
     */
    public static function uploadSchema(bool $imagesOnly = true, ?string $category = null): array
    {
        $types = $imagesOnly
            ? ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif', 'image/svg+xml']
            : ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif', 'image/svg+xml', 'application/pdf'];

        return [
            FileUpload::make('upload')
                ->label('File')
                ->required()
                ->disk('local')
                ->directory('media-incoming')
                ->visibility('private')
                ->storeFileNamesIn('upload_name')
                ->acceptedFileTypes($types)
                ->maxSize((int) config('softec.media.max_upload_kb', 8192))
                ->helperText('JPEG, PNG, WebP, AVIF, GIF or SVG'.($imagesOnly ? '' : ', or PDF').' up to '.round(config('softec.media.max_upload_kb', 8192) / 1024).' MB. Location metadata is removed automatically.'),
            TextInput::make('title')->maxLength(150),
            TextInput::make('alt_text')
                ->label('Alternative text')
                ->maxLength(250)
                ->helperText('Describe the image for screen-reader users. Leave empty only for purely decorative images.'),
        ];
    }

    public static function optionLabel(Media $media): string
    {
        $title = e($media->label());

        if (! $media->isImage()) {
            return '<span class="flex items-center gap-2"><span class="text-xs font-semibold uppercase text-gray-500">'.e($media->extension).'</span>'.$title.'</span>';
        }

        $src = e($media->variantUrl(480) ?? $media->url());

        return '<span class="flex items-center gap-3"><img src="'.$src.'" alt="" class="h-9 w-12 rounded-md object-cover ring-1 ring-gray-950/10 dark:ring-white/10" loading="lazy">'
            .'<span class="truncate">'.$title.'</span></span>';
    }
}
