<?php

namespace App\Filament\Forms;

use App\Models\Media;
use App\Support\Media\MediaLibrary;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Media library picker for settings that store a plain media id (not an
 * Eloquent relationship), with thumbnails and inline upload.
 */
final class MediaIdSelect
{
    public static function make(string $field, string $label, bool $imagesOnly = true, ?string $category = null): Select
    {
        $query = fn (): Builder => Media::query()
            ->when($imagesOnly, fn (Builder $q) => $q->where('mime_type', 'like', 'image/%'))
            ->latest('id');

        return Select::make($field)
            ->label($label)
            ->options(fn (): array => $query()->limit(50)->get()->mapWithKeys(fn (Media $media) => [$media->getKey() => MediaPicker::optionLabel($media)])->all())
            ->getSearchResultsUsing(fn (string $search): array => $query()
                ->where(fn (Builder $q) => $q->where('title', 'like', '%'.addcslashes($search, '%_\\').'%')
                    ->orWhere('original_name', 'like', '%'.addcslashes($search, '%_\\').'%'))
                ->limit(50)->get()
                ->mapWithKeys(fn (Media $media) => [$media->getKey() => MediaPicker::optionLabel($media)])->all())
            ->getOptionLabelUsing(fn ($value): ?string => ($media = Media::query()->find($value)) ? MediaPicker::optionLabel($media) : null)
            ->allowHtml()
            ->searchable()
            ->native(false)
            ->rule(fn () => function (string $attribute, $value, \Closure $fail) use ($query): void {
                if (filled($value) && ! $query()->whereKey($value)->exists()) {
                    $fail('Please choose a valid file from the media library.');
                }
            })
            ->createOptionForm(MediaPicker::uploadSchema($imagesOnly, $category))
            ->createOptionUsing(fn (array $data): int => app(MediaLibrary::class)->importFromDisk('local', $data['upload'], [
                'alt_text' => $data['alt_text'] ?? null,
                'title' => $data['title'] ?? null,
                'category' => $category,
            ], $data['upload_name'] ?? null)->getKey())
            ->createOptionAction(fn (Action $action) => $action
                ->modalHeading('Upload to media library')
                ->visible(fn (): bool => (bool) Auth::user()?->can('media.manage')));
    }
}
