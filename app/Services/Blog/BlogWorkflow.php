<?php

namespace App\Services\Blog;

use App\Enums\PostStatus;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Models\Author;
use App\Models\BlogPost;
use App\Models\User;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Editorial state transitions for blog posts. Every transition re-checks the
 * policy (defence in depth on top of action visibility) and notifies the
 * people who need to act next.
 */
class BlogWorkflow
{
    /**
     * The author profile for a user, created on first use.
     */
    public function authorFor(User $user): Author
    {
        return $user->author ?? Author::query()->create([
            'user_id' => $user->getKey(),
            'name' => $user->name,
            'job_title' => $user->job_title,
            'is_active' => true,
        ]);
    }

    /**
     * Statuses the given user may choose in the editor.
     *
     * @return array<int, PostStatus>
     */
    public function allowedStatuses(User $user, ?BlogPost $post = null): array
    {
        if ($user->can('blogs.publish')) {
            return PostStatus::cases();
        }

        return [PostStatus::Draft, PostStatus::InReview];
    }

    public function submitForReview(User $actor, BlogPost $post): void
    {
        Gate::forUser($actor)->authorize('update', $post);
        $this->assertStatus($post, [PostStatus::Draft]);

        // A new review cycle starts: clear any previous approval.
        $post->forceFill(['status' => PostStatus::InReview, 'reviewed_by' => null, 'reviewed_at' => null])->save();

        $this->notify(
            $this->usersWith('blogs.review')->reject(fn (User $user) => $user->is($actor)),
            'Post submitted for review',
            '“'.$post->title.'” by '.$actor->name.' is ready for review.',
            $post,
        );
    }

    public function approve(User $actor, BlogPost $post, ?string $notes = null): void
    {
        Gate::forUser($actor)->authorize('review', $post);

        $post->forceFill([
            'reviewed_by' => $actor->getKey(),
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ])->save();

        $this->notify(
            $this->usersWith('blogs.publish')->reject(fn (User $user) => $user->is($actor)),
            'Post approved — ready to publish',
            '“'.$post->title.'” was approved by '.$actor->name.'.',
            $post,
        );
    }

    public function requestChanges(User $actor, BlogPost $post, string $notes): void
    {
        Gate::forUser($actor)->authorize('review', $post);

        $post->forceFill([
            'status' => PostStatus::Draft,
            'review_notes' => $notes,
            'reviewed_by' => $actor->getKey(),
            'reviewed_at' => now(),
        ])->save();

        $this->notify($this->authorUsers($post), 'Changes requested', $actor->name.' requested changes to “'.$post->title.'”.', $post);
    }

    public function publishNow(User $actor, BlogPost $post): void
    {
        Gate::forUser($actor)->authorize('publish', $post);

        $post->forceFill([
            'status' => PostStatus::Published,
            'published_at' => $post->published_at !== null && $post->published_at->isPast() ? $post->published_at : now(),
        ])->save();

        $this->notify($this->authorUsers($post)->reject(fn (User $user) => $user->is($actor)), 'Post published', '“'.$post->title.'” is now live.', $post);
    }

    public function schedule(User $actor, BlogPost $post, CarbonInterface $publishAt): void
    {
        Gate::forUser($actor)->authorize('publish', $post);

        if ($publishAt->isPast()) {
            throw ValidationException::withMessages(['published_at' => 'Choose a date and time in the future.']);
        }

        $post->forceFill(['status' => PostStatus::Scheduled, 'published_at' => $publishAt])->save();

        $this->notify($this->authorUsers($post)->reject(fn (User $user) => $user->is($actor)), 'Post scheduled', '“'.$post->title.'” will be published on '.$publishAt->format('M j, Y H:i').'.', $post);
    }

    public function unpublish(User $actor, BlogPost $post): void
    {
        Gate::forUser($actor)->authorize('publish', $post);
        $this->assertStatus($post, [PostStatus::Published, PostStatus::Scheduled, PostStatus::Archived]);

        $post->forceFill(['status' => PostStatus::Draft])->save();
    }

    public function archive(User $actor, BlogPost $post): void
    {
        Gate::forUser($actor)->authorize('publish', $post);

        $post->forceFill(['status' => PostStatus::Archived])->save();
    }

    /**
     * Publishes scheduled posts whose time has come. Returns how many changed.
     */
    public function publishDueScheduledPosts(): int
    {
        $count = 0;

        BlogPost::query()
            ->where('status', PostStatus::Scheduled->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->each(function (BlogPost $post) use (&$count): void {
                $post->status = PostStatus::Published;
                $post->save();
                $count++;
            });

        return $count;
    }

    /**
     * @param  array<int, PostStatus>  $allowed
     */
    private function assertStatus(BlogPost $post, array $allowed): void
    {
        if (! in_array($post->status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => 'This action is not available for posts that are '.$post->status->getLabel().'.']);
        }
    }

    /**
     * @return Collection<int, User>
     */
    private function usersWith(string $permission): Collection
    {
        return User::query()->active()->get()->filter(fn (User $user) => $user->can($permission))->values();
    }

    /**
     * @return Collection<int, User>
     */
    private function authorUsers(BlogPost $post): Collection
    {
        $post->loadMissing(['author.user', 'creator']);

        return collect([$post->author?->user, $post->creator])->filter()->unique('id')->values();
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function notify(Collection $users, string $title, string $body, BlogPost $post): void
    {
        if ($users->isEmpty()) {
            return;
        }

        $url = rescue(fn () => BlogPostResource::getUrl('view', ['record' => $post]), null, false);

        Notification::make()
            ->title($title)
            ->body($body)
            ->icon('heroicon-o-newspaper')
            ->actions($url ? [Action::make('open')->label('Open post')->url($url)->markAsRead()] : [])
            ->sendToDatabase($users);
    }
}
