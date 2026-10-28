<?php

namespace App\Filament\Resources\BlogComments;

use App\Enums\CommentStatus;
use App\Filament\AdminNavigationGroup;
use App\Filament\Resources\BlogComments\Pages\ManageBlogComments;
use App\Models\BlogComment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Moderation queue for blog comments. Comments appear publicly only once approved.
 */
class BlogCommentResource extends Resource
{
    protected static ?string $model = BlogComment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Blog;

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Comments';

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('post.title')->label('Post'),
            TextEntry::make('name'),
            // Visible only inside the admin panel, which only moderators can open.
            TextEntry::make('email')->copyable(),
            TextEntry::make('status')->badge(),
            TextEntry::make('body')->columnSpanFull(),
            TextEntry::make('created_at')->dateTime(),
            TextEntry::make('moderated_at')->dateTime()->placeholder('—'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('post'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->weight('medium')->description(fn (BlogComment $record): ?string => $record->post?->title),
                TextColumn::make('body')->label('Comment')->limit(90)->wrap()->searchable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(CommentStatus::class)->default(CommentStatus::Pending->value),
                SelectFilter::make('blog_post_id')->label('Post')->relationship('post', 'title')->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                self::setStatus('approve', 'Approve', CommentStatus::Approved, 'heroicon-o-check', 'success'),
                self::setStatus('spam', 'Mark as spam', CommentStatus::Spam, 'heroicon-o-no-symbol', 'danger'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::setStatusBulk('approveSelected', 'Approve', CommentStatus::Approved, 'heroicon-o-check'),
                    self::setStatusBulk('spamSelected', 'Mark as spam', CommentStatus::Spam, 'heroicon-o-no-symbol'),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text')
            ->emptyStateHeading('No comments to moderate')
            ->emptyStateDescription('New comments wait here until they are approved.');
    }

    private static function setStatus(string $name, string $label, CommentStatus $status, string $icon, string $color): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->authorize(fn (BlogComment $record): bool => (bool) Auth::user()?->can('update', $record))
            ->visible(fn (BlogComment $record): bool => $record->status !== $status)
            ->action(function (BlogComment $record) use ($status): void {
                self::moderate($record, $status);
                Notification::make()->title('Comment updated')->success()->send();
            });
    }

    private static function setStatusBulk(string $name, string $label, CommentStatus $status, string $icon): BulkAction
    {
        return BulkAction::make($name)
            ->label($label)
            ->icon($icon)
            ->requiresConfirmation()
            ->authorize(fn (): bool => (bool) Auth::user()?->can('comments.moderate'))
            ->action(function (Collection $records) use ($status): void {
                $records->each(fn (BlogComment $record) => self::moderate($record, $status));
                Notification::make()->title($records->count().' comment(s) updated')->success()->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function moderate(BlogComment $comment, CommentStatus $status): void
    {
        $comment->forceFill([
            'status' => $status,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
        ])->save();
    }

    public static function getNavigationBadge(): ?string
    {
        if (! Auth::user()?->can('comments.moderate')) {
            return null;
        }

        $count = BlogComment::query()->where('status', CommentStatus::Pending->value)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBlogComments::route('/'),
        ];
    }
}
