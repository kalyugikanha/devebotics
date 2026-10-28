<?php

namespace App\Filament\Resources\Media\Actions;

use App\Filament\Forms\MediaPicker;
use App\Jobs\GenerateMediaVariants;
use App\Models\Media;
use App\Support\Media\MediaException;
use App\Support\Media\MediaLibrary;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * Swaps the stored file of a media item while keeping its ID, so every page
 * that references it shows the new file immediately.
 */
final class ReplaceMediaFileAction
{
    public static function make(): Action
    {
        return Action::make('replaceFile')
            ->label('Replace file')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            ->modalHeading('Replace file')
            ->modalDescription('Upload a new version. Everything that uses this media item will show the new file. The previous file is deleted.')
            ->authorize(fn (Media $record): bool => Gate::allows('update', $record))
            ->schema(fn (Media $record): array => [MediaPicker::uploadSchema(imagesOnly: $record->isImage())[0]])
            ->action(function (Media $record, array $data, Action $action): void {
                $library = app(MediaLibrary::class);

                try {
                    $replacement = $library->importFromDisk('local', $data['upload'], ['category' => $record->category], $data['upload_name'] ?? null);
                } catch (MediaException $exception) {
                    Notification::make()->title('Upload rejected')->body($exception->getMessage())->danger()->send();
                    $action->halt();

                    return;
                }

                if ($record->isImage() !== $replacement->isImage()) {
                    $replacement->delete();
                    Notification::make()->title('Please upload the same kind of file (image or document).')->danger()->send();
                    $action->halt();

                    return;
                }

                $previous = ['disk' => $record->disk, 'files' => array_filter([$record->path, ...array_values($record->variants['webp'] ?? [])])];
                $replacement->refresh();

                $record->forceFill([
                    'disk' => $replacement->disk,
                    'path' => $replacement->path,
                    'original_name' => $replacement->original_name,
                    'mime_type' => $replacement->mime_type,
                    'extension' => $replacement->extension,
                    'size' => $replacement->size,
                    'width' => $replacement->width,
                    'height' => $replacement->height,
                    'checksum' => $replacement->checksum,
                    'variants' => $replacement->variants,
                ])->save();

                // Remove the temporary row without triggering file deletion (the files now belong to $record).
                $replacement->deleteQuietly();

                Storage::disk($previous['disk'])->delete(array_diff($previous['files'], array_filter([$record->path, ...array_values($record->variants['webp'] ?? [])])));

                if ($record->variants === null && $library->supportsVariants($record->mime_type)) {
                    GenerateMediaVariants::dispatch($record->getKey());
                }

                Notification::make()->title('File replaced')->success()->send();
            });
    }
}
