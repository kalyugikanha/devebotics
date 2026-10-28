<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates a unique, URL-safe slug from the model's slug source column
 * when none is supplied, and normalises manually entered slugs.
 *
 * @mixin Model
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            /** @var Model&self $model */
            $current = $model->getAttribute('slug');

            if ($model->exists && filled($current) && ! $model->isDirty('slug')) {
                return;
            }

            $slug = Str::slug((string) (filled($current) ? $current : $model->getAttribute($model->slugSourceColumn())));

            if ($slug === '') {
                $slug = Str::lower(Str::random(8));
            }

            $model->setAttribute('slug', $model->uniqueSlug($slug));
        });
    }

    public function slugSourceColumn(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'title';
    }

    public function uniqueSlug(string $slug): string
    {
        $candidate = Str::limit($slug, 180, '');
        $suffix = 2;

        while ($this->slugExists($candidate)) {
            $candidate = Str::limit($slug, 175, '').'-'.$suffix++;
        }

        return $candidate;
    }

    protected function slugExists(string $slug): bool
    {
        $query = static::query()->where('slug', $slug);

        if (method_exists($this, 'bootSoftDeletes')) {
            $query->withTrashed();
        }

        if ($this->exists) {
            $query->whereKeyNot($this->getKey());
        }

        return $query->exists();
    }
}
