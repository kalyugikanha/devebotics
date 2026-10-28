<?php

namespace App\Filament\Resources\Media\Actions;

use App\Models\Media;
use App\Support\Media\MediaUsage;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Delete actions that refuse to remove files still referenced by content.
 */
final class MediaDeletion
{
    public static function action(): DeleteAction
    {
        return DeleteAction::make()
            ->modalDescription('The file and its responsive variants are permanently deleted. Files that are in use cannot be deleted.')
            ->before(function (DeleteAction $action, Media $record): void {
                $usages = app(MediaUsage::class)->usages($record);

                if ($usages !== []) {
                    Notification::make()
                        ->title('This file is in use and cannot be deleted')
                        ->body('Used by: '.implode('; ', $usages))
                        ->danger()
                        ->persistent()
                        ->send();

                    $action->cancel();
                }
            });
    }

    public static function bulkAction(): BulkAction
    {
        return BulkAction::make('delete')
            ->label('Delete selected')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('Files that are still used by content are skipped.')
            ->authorize(fn (): bool => (bool) Auth::user()?->can('media.delete'))
            ->action(function (Collection $records): void {
                $usage = app(MediaUsage::class);
                $deleted = 0;
                $skipped = [];

                foreach ($records as $record) {
                    if (! Gate::allows('delete', $record) || $usage->isUsed($record)) {
                        $skipped[] = $record->label();

                        continue;
                    }

                    $record->delete();
                    $deleted++;
                }

                Notification::make()
                    ->title("{$deleted} file(s) deleted")
                    ->body($skipped !== [] ? 'Skipped (in use): '.implode(', ', array_slice($skipped, 0, 10)) : null)
                    ->status($skipped !== [] ? 'warning' : 'success')
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
