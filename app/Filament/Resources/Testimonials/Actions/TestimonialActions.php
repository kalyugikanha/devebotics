<?php

namespace App\Filament\Resources\Testimonials\Actions;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use App\Services\TestimonialModeration;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Moderation actions shared by the table and record pages.
 */
final class TestimonialActions
{
    /**
     * @return array<int, Action>
     */
    public static function all(): array
    {
        return [self::approve(), self::reject(), self::archive(), self::toggleFeatured(), self::replyPrivately(), self::viewPhoto()];
    }

    public static function approve(): Action
    {
        return Action::make('approve')
            ->label('Approve & publish')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->authorize(fn (Testimonial $record): bool => Gate::allows('moderate', $record))
            ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Approved && ! $record->trashed())
            ->modalHeading('Approve this feedback?')
            ->modalDescription(fn (Testimonial $record): string => $record->publish_permission
                ? 'It will appear on the website'.($record->display_identity_consent ? ' with the client’s name and company.' : ' as “Verified client” (no identity consent).')
                : 'The client did NOT give permission to publish this feedback, so it cannot be approved for public display.')
            ->schema(fn (Testimonial $record): array => $record->publish_permission ? [
                Toggle::make('notify_client')->label('Email the client that their feedback is live')->default(filled($record->email)),
            ] : [])
            ->modalSubmitAction(fn (Action $action, Testimonial $record) => $record->publish_permission ? $action : false)
            ->action(function (Testimonial $record, array $data): void {
                app(TestimonialModeration::class)->approve(Auth::user(), $record, (bool) ($data['notify_client'] ?? false));
                Notification::make()->title('Feedback approved')->success()->send();
            });
    }

    public static function reject(): Action
    {
        return Action::make('reject')
            ->label('Reject')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->authorize(fn (Testimonial $record): bool => Gate::allows('moderate', $record))
            ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Rejected && ! $record->trashed())
            ->schema([
                Textarea::make('note')->label('Internal moderation note (optional)')->rows(3)->maxLength(1000),
            ])
            ->action(function (Testimonial $record, array $data): void {
                app(TestimonialModeration::class)->reject(Auth::user(), $record, $data['note'] ?? null);
                Notification::make()->title('Feedback rejected')->success()->send();
            });
    }

    public static function archive(): Action
    {
        return Action::make('archive')
            ->label('Archive')
            ->icon('heroicon-o-archive-box')
            ->color('gray')
            ->requiresConfirmation()
            ->authorize(fn (Testimonial $record): bool => Gate::allows('moderate', $record))
            ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Archived && ! $record->trashed())
            ->action(function (Testimonial $record): void {
                app(TestimonialModeration::class)->archive(Auth::user(), $record);
                Notification::make()->title('Feedback archived')->success()->send();
            });
    }

    public static function toggleFeatured(): Action
    {
        return Action::make('toggleFeatured')
            ->label(fn (Testimonial $record): string => $record->is_featured ? 'Unfeature' : 'Feature')
            ->icon(fn (Testimonial $record): string => $record->is_featured ? 'heroicon-o-star' : 'heroicon-s-star')
            ->color('warning')
            ->authorize(fn (Testimonial $record): bool => Gate::allows('moderate', $record))
            ->visible(fn (Testimonial $record): bool => $record->status === TestimonialStatus::Approved)
            ->action(function (Testimonial $record): void {
                app(TestimonialModeration::class)->toggleFeatured(Auth::user(), $record);
                Notification::make()->title($record->is_featured ? 'Featured on the website' : 'No longer featured')->success()->send();
            });
    }

    public static function replyPrivately(): Action
    {
        return Action::make('replyPrivately')
            ->label('Reply privately')
            ->icon('heroicon-o-envelope')
            ->color('info')
            ->authorize(fn (Testimonial $record): bool => Gate::allows('moderate', $record))
            ->visible(fn (Testimonial $record): bool => filled($record->email) && ! $record->trashed())
            ->modalDescription('Your reply is emailed to the client and stored internally. It is never shown on the website.')
            ->schema([
                Textarea::make('reply')->required()->rows(6)->maxLength(3000)
                    ->default(fn (Testimonial $record): ?string => $record->admin_reply),
            ])
            ->modalSubmitActionLabel('Send reply')
            ->action(function (Testimonial $record, array $data): void {
                app(TestimonialModeration::class)->replyPrivately(Auth::user(), $record, (string) $data['reply']);
                Notification::make()->title('Reply sent')->success()->send();
            });
    }

    public static function viewPhoto(): Action
    {
        return Action::make('viewPhoto')
            ->label('Submitted photo')
            ->icon('heroicon-o-photo')
            ->color('gray')
            ->url(fn (Testimonial $record): string => route('files.testimonial-photo', $record), shouldOpenInNewTab: true)
            ->authorize(fn (Testimonial $record): bool => Gate::allows('viewPrivatePhoto', $record))
            ->visible(fn (Testimonial $record): bool => filled($record->submitted_photo_path));
    }
}
