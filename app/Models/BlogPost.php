<?php

namespace App\Models;

use App\Casts\SanitizedHtml;
use App\Contracts\HasPublicUrl;
use App\Enums\PostStatus;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\FlagsDemoContent;
use App\Models\Concerns\HasRevisions;
use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\RecordsSlugRedirects;
use App\Models\Concerns\TracksEditors;
use Database\Factories\BlogPostFactory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

#[Fillable([
    'author_id', 'blog_category_id', 'title', 'slug', 'excerpt', 'body', 'featured_media_id', 'status',
    'published_at', 'is_featured', 'allow_comments', 'review_notes', 'is_ai_assisted', 'is_demo',
])]
class BlogPost extends Model implements HasPublicUrl
{
    /** @use HasFactory<BlogPostFactory> */
    use Auditable, FlagsDemoContent, HasFactory, HasRevisions, HasSeo, HasSlug, RecordsSlugRedirects, SoftDeletes, TracksEditors;

    public const WORDS_PER_MINUTE = 220;

    /**
     * @var array<int, string>
     */
    protected array $revisionable = ['title', 'slug', 'excerpt', 'body', 'status', 'published_at', 'blog_category_id'];

    protected function casts(): array
    {
        return [
            'body' => SanitizedHtml::class,
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'is_ai_assisted' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post): void {
            static::guardPublishingChanges($post);

            $post->reading_time_minutes = static::estimateReadingTime((string) $post->body);

            if ($post->status === PostStatus::Published && $post->published_at === null) {
                $post->published_at = now();
            }

            // A "published" post with a future date is a scheduled post.
            if ($post->status === PostStatus::Published && $post->published_at->isFuture()) {
                $post->status = PostStatus::Scheduled;
            }

            if ($post->isDirty('status') && $post->status === PostStatus::InReview) {
                $post->submitted_at = now();
            }
        });
    }

    /**
     * Server-side editorial guard: when an authenticated user changes the
     * status into or out of Scheduled / Published / Archived, or moves the
     * publication date of a live post, they must hold `blogs.publish`. This
     * applies to every write path (forms, bulk actions, tampered payloads).
     * Console contexts (scheduler, seeders) have no user and are unaffected.
     *
     * @throws AuthorizationException
     */
    protected static function guardPublishingChanges(BlogPost $post): void
    {
        $user = Auth::user();

        if ($user === null || $user->can('blogs.publish')) {
            return;
        }

        $restricted = [PostStatus::Scheduled, PostStatus::Published, PostStatus::Archived];
        $original = $post->exists ? $post->getOriginal('status') : null;
        $original = $original instanceof PostStatus ? $original : PostStatus::tryFrom((string) $original);

        $changesStatus = $post->isDirty('status') && (in_array($post->status, $restricted, true) || in_array($original, $restricted, true));
        $movesLiveDate = $post->exists && $post->isDirty('published_at') && in_array($original, $restricted, true);
        $createsPublished = ! $post->exists && in_array($post->status, $restricted, true);

        if ($changesStatus || $movesLiveDate || $createsPublished) {
            throw new AuthorizationException('You are not allowed to publish, schedule, unpublish or archive posts.');
        }
    }

    /**
     * Restoring a revision brings back content only — never the publication
     * status or date, which are controlled by the publishing workflow.
     */
    public function restoreRevision(ContentRevision $revision): void
    {
        $this->fill(Arr::except(Arr::only($revision->data, $this->revisionableAttributes()), ['status', 'published_at']));
        $this->save();
    }

    public static function estimateReadingTime(string $html): int
    {
        $words = str_word_count(strip_tags($html));

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }

    /**
     * Visible posts: published, or scheduled with a publication time that has passed
     * (so visibility does not depend on the scheduler having run).
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereIn('blog_posts.status', [PostStatus::Published->value, PostStatus::Scheduled->value])
            ->whereNotNull('blog_posts.published_at')
            ->where('blog_posts.published_at', '<=', now());
    }

    public function isPubliclyVisible(): bool
    {
        return in_array($this->status, [PostStatus::Published, PostStatus::Scheduled], true)
            && $this->published_at !== null
            && $this->published_at->lessThanOrEqualTo(now())
            && ! $this->trashed();
    }

    /**
     * @return BelongsTo<Author, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * @return BelongsTo<BlogCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * @return BelongsToMany<BlogTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    /**
     * @return HasMany<BlogComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function publicUrl(): string
    {
        return route('blog.show', $this->slug);
    }

    public function publicPathForSlug(string $slug): string
    {
        return '/blog/'.$slug;
    }
}
