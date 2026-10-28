<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Enums\PostStatus;
use App\Filament\Resources\BlogPosts\Actions\BlogWorkflowActions;
use App\Filament\Tables\ContentTable;
use App\Models\BlogPost;
use App\Services\Blog\BlogWorkflow;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['author', 'category']))
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(70)
                    ->description(fn (BlogPost $record): string => collect([$record->author?->name, $record->category?->name])->filter()->implode(' · ')),
                ContentTable::status()
                    ->description(fn (BlogPost $record): ?string => $record->status === PostStatus::InReview && $record->reviewed_at ? 'Approved' : null),
                ContentTable::featured(),
                TextColumn::make('reading_time_minutes')->label('Read')->suffix(' min')->sortable()->toggleable(isToggledHiddenByDefault: true),
                ContentTable::publishedAt(),
                ContentTable::updatedAt(),
                ContentTable::demo(),
            ])
            ->filters([
                ContentTable::statusFilter(PostStatus::class),
                SelectFilter::make('blog_category_id')->label('Category')->relationship('category', 'name'),
                SelectFilter::make('author_id')->label('Author')->relationship('author', 'name'),
                ContentTable::featuredFilter(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ContentTable::viewOnSite(),
                ContentTable::preview(),
                ViewAction::make()->hidden(fn (BlogPost $record): bool => Gate::allows('update', $record)),
                EditAction::make(),
                ActionGroup::make([
                    ...BlogWorkflowActions::all(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::bulkTransition('publishSelected', 'Publish now', 'heroicon-o-globe-alt', 'success', fn (BlogWorkflow $w, BlogPost $p) => $w->publishNow(Auth::user(), $p)),
                    self::bulkTransition('unpublishSelected', 'Move to draft', 'heroicon-o-eye-slash', 'gray', fn (BlogWorkflow $w, BlogPost $p) => $w->unpublish(Auth::user(), $p)),
                    self::bulkTransition('archiveSelected', 'Archive', 'heroicon-o-archive-box', 'warning', fn (BlogWorkflow $w, BlogPost $p) => $w->archive(Auth::user(), $p)),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-newspaper')
            ->emptyStateHeading('No posts yet')
            ->emptyStateDescription('Write your first article. Drafts are private until an editor publishes them.')
            ->emptyStateActions([CreateAction::make()]);
    }

    /**
     * Bulk publishing actions require `blogs.publish`; each record is
     * re-authorised and transitioned individually.
     */
    private static function bulkTransition(string $name, string $label, string $icon, string $color, \Closure $transition): BulkAction
    {
        return BulkAction::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->authorize(fn (): bool => (bool) Auth::user()?->can('blogs.publish'))
            ->action(function (Collection $records) use ($transition): void {
                $done = 0;

                foreach ($records as $record) {
                    try {
                        $transition(app(BlogWorkflow::class), $record);
                        $done++;
                    } catch (Throwable) {
                        // Skipped: not permitted or not applicable for this post's status.
                    }
                }

                Notification::make()->title("{$done} post(s) updated")->success()->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
