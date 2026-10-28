<?php

namespace App\Models;

use App\Enums\CommentStatus;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['blog_post_id', 'parent_id', 'name', 'email', 'body', 'status', 'ip_hash'])]
#[Hidden(['email', 'ip_hash'])]
class BlogComment extends Model
{
    use Auditable;

    /**
     * @var array<int, string>
     */
    protected array $auditExclude = ['email', 'ip_hash'];

    protected function casts(): array
    {
        return [
            'email' => 'encrypted',
            'status' => CommentStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', CommentStatus::Approved->value);
    }

    /**
     * @return BelongsTo<BlogPost, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    /**
     * @return BelongsTo<BlogComment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<BlogComment, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
