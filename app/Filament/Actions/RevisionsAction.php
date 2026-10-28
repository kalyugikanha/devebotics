<?php

namespace App\Filament\Actions;

use App\Models\ContentRevision;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * Lists stored revisions of a record and restores a selected one.
 */
final class RevisionsAction
{
    public static function make(): Action
    {
        return Action::make('revisions')
            ->label('Revisions')
            ->icon('heroicon-o-clock')
            ->color('gray')
            ->modalHeading('Revision history')
            ->modalDescription('Restoring copies the selected version into the current record. The current version is kept as a new revision.')
            ->modalSubmitActionLabel('Restore selected version')
            ->authorize(fn (Model $record): bool => Gate::allows('update', $record))
            ->visible(fn (Model $record): bool => method_exists($record, 'revisions'))
            ->schema(fn (Model $record): array => [
                Select::make('revision_id')
                    ->label('Version')
                    ->required()
                    ->options(
                        $record->revisions()->with('user')->limit(30)->get()
                            ->mapWithKeys(fn (ContentRevision $revision): array => [
                                $revision->getKey() => $revision->created_at?->format('M j, Y H:i').' — '.($revision->user?->name ?? 'System').' — '.Str::limit((string) $revision->summary, 60),
                            ])
                            ->all(),
                    ),
            ])
            ->action(function (Model $record, array $data): void {
                $revision = $record->revisions()->whereKey($data['revision_id'])->firstOrFail();
                $record->restoreRevision($revision);

                Notification::make()->title('Revision restored')->success()->send();
            });
    }
}
