<?php

namespace App\Models\Concerns;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Draft / published / archived lifecycle with optional future publication date.
 *
 * @mixin Model
 */
trait Publishable
{
    public function initializePublishable(): void
    {
        $this->mergeCasts([
            'status' => ContentStatus::class,
            'published_at' => 'datetime',
        ]);
    }

    public static function bootPublishable(): void
    {
        static::saving(function (Model $model): void {
            if ($model->getAttribute('status') === ContentStatus::Published && blank($model->getAttribute('published_at'))) {
                $model->setAttribute('published_at', now());
            }
        });
    }

    /**
     * Records visible to anonymous visitors.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where($this->qualifyColumn('status'), ContentStatus::Published->value)
            ->where(function (Builder $query): void {
                $query->whereNull($this->qualifyColumn('published_at'))
                    ->orWhere($this->qualifyColumn('published_at'), '<=', now());
            });
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === ContentStatus::Published
            && ($this->published_at === null || $this->published_at->lessThanOrEqualTo(now()))
            && ! (method_exists($this, 'trashed') && $this->trashed());
    }
}
