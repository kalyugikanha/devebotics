<?php

namespace App\Filament\Resources\BlogPosts\Actions;

use App\Enums\PostStatus;
use App\Models\BlogPost;
use App\Services\Blog\BlogWorkflow;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Editorial workflow actions, usable as table row actions and page header actions.
 * Visibility mirrors the policy; BlogWorkflow re-authorises every transition.
 */
final class BlogWorkflowActions
{
    /**
     * @return array<int, Action>
     */
    public static function all(): array
    {
        return [
            self::submitForReview(),
            self::approve(),
            self::requestChanges(),
            self::publishNow(),
            self::schedule(),
            self::unpublish(),
            self::archive(),
        ];
    }

    public static function submitForReview(): Action
    {
        return Action::make('submitForReview')
            ->label('Submit for review')
            ->icon('heroicon-o-paper-airplane')
            ->color('info')
            ->requiresConfirmation()
            ->modalDescription('Editors will be notified. You will not be able to edit the post while it is in review.')
            ->visible(fn (BlogPost $record): bool => $record->status === PostStatus::Draft && ! $record->trashed() && Gate::allows('update', $record))
            ->action(function (BlogPost $record): void {
                app(BlogWorkflow::class)->submitForReview(Auth::user(), $record);
                Notification::make()->title('Submitted for review')->success()->send();
            });
    }

    public static function approve(): Action
    {
        return Action::make('approve')
            ->label('Approve')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->schema([
                Textarea::make('notes')->label('Notes for the publisher (optional)')->rows(3)->maxLength(2000),
            ])
            ->modalSubmitActionLabel('Approve post')
            ->visible(fn (BlogPost $record): bool => Gate::allows('review', $record) && $record->reviewed_at === null)
            ->action(function (BlogPost $record, array $data): void {
                app(BlogWorkflow::class)->approve(Auth::user(), $record, $data['notes'] ?? null);
                Notification::make()->title('Post approved')->success()->send();
            });
    }

    public static function requestChanges(): Action
    {
        return Action::make('requestChanges')
            ->label('Request changes')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('warning')
            ->schema([
                Textarea::make('notes')->label('What should be changed?')->required()->rows(4)->maxLength(2000),
            ])
            ->modalSubmitActionLabel('Send back to author')
            ->visible(fn (BlogPost $record): bool => Gate::allows('review', $record))
            ->action(function (BlogPost $record, array $data): void {
                app(BlogWorkflow::class)->requestChanges(Auth::user(), $record, (string) $data['notes']);
                Notification::make()->title('Sent back to the author')->success()->send();
            });
    }

    public static function publishNow(): Action
    {
        return Action::make('publishNow')
            ->label('Publish now')
            ->icon('heroicon-o-globe-alt')
            ->color('success')
            ->requiresConfirmation()
            ->authorize(fn (BlogPost $record): bool => Gate::allows('publish', $record))
            ->visible(fn (BlogPost $record): bool => ! $record->trashed() && ! $record->isPubliclyVisible())
            ->action(function (BlogPost $record): void {
                app(BlogWorkflow::class)->publishNow(Auth::user(), $record);
                Notification::make()->title('Post published')->success()->send();
            });
    }

    public static function schedule(): Action
    {
        return Action::make('schedule')
            ->label('Schedule')
            ->icon('heroicon-o-clock')
            ->color('warning')
            ->schema([
                DateTimePicker::make('published_at')
                    ->label('Publish on')
                    ->required()
                    ->seconds(false)
                    ->native(false)
                    ->minDate(now())
                    ->default(now()->addDay()->setTime(9, 0)),
            ])
            ->modalSubmitActionLabel('Schedule')
            ->authorize(fn (BlogPost $record): bool => Gate::allows('publish', $record))
            ->visible(fn (BlogPost $record): bool => ! $record->trashed() && $record->status !== PostStatus::Published)
            ->action(function (BlogPost $record, array $data): void {
                app(BlogWorkflow::class)->schedule(Auth::user(), $record, Carbon::parse($data['published_at']));
                Notification::make()->title('Post scheduled')->success()->send();
            });
    }

    public static function unpublish(): Action
    {
        return Action::make('unpublish')
            ->label('Unpublish')
            ->icon('heroicon-o-eye-slash')
            ->color('gray')
            ->requiresConfirmation()
            ->modalDescription('The post will be removed from the website and returned to draft.')
            ->authorize(fn (BlogPost $record): bool => Gate::allows('publish', $record))
            ->visible(fn (BlogPost $record): bool => in_array($record->status, [PostStatus::Published, PostStatus::Scheduled, PostStatus::Archived], true))
            ->action(function (BlogPost $record): void {
                app(BlogWorkflow::class)->unpublish(Auth::user(), $record);
                Notification::make()->title('Post moved to draft')->success()->send();
            });
    }

    public static function archive(): Action
    {
        return Action::make('archive')
            ->label('Archive')
            ->icon('heroicon-o-archive-box')
            ->color('danger')
            ->requiresConfirmation()
            ->authorize(fn (BlogPost $record): bool => Gate::allows('publish', $record))
            ->visible(fn (BlogPost $record): bool => ! $record->trashed() && $record->status !== PostStatus::Archived)
            ->action(function (BlogPost $record): void {
                app(BlogWorkflow::class)->archive(Auth::user(), $record);
                Notification::make()->title('Post archived')->success()->send();
            });
    }
}
