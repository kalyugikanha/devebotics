<?php

namespace App\Models\Concerns;

use App\Models\ContentRevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Keeps snapshot history of editorial content so earlier versions can be
 * reviewed and restored from the admin panel.
 *
 * @mixin Model
 */
trait HasRevisions
{
    public static int $maxRevisions = 30;

    public static function bootHasRevisions(): void
    {
        static::saved(function (Model $model): void {
            /** @var Model&self $model */
            $tracked = $model->revisionableAttributes();

            if (! $model->wasRecentlyCreated && ! $model->wasChanged($tracked)) {
                return;
            }

            $model->revisions()->create([
                'user_id' => Auth::id(),
                'summary' => $model->wasRecentlyCreated ? 'Created' : 'Updated: '.implode(', ', array_keys(Arr::only($model->getChanges(), $tracked))),
                'data' => Arr::only($model->getAttributes(), $tracked),
                'created_at' => now(),
            ]);

            $staleIds = $model->revisions()->latest('id')->skip(static::$maxRevisions)->take(100)->pluck('id');

            if ($staleIds->isNotEmpty()) {
                ContentRevision::query()->whereKey($staleIds)->delete();
            }
        });
    }

    /**
     * @return MorphMany<ContentRevision, $this>
     */
    public function revisions(): MorphMany
    {
        return $this->morphMany(ContentRevision::class, 'revisionable')->latest('id');
    }

    /**
     * @return array<int, string>
     */
    public function revisionableAttributes(): array
    {
        return property_exists($this, 'revisionable') ? $this->revisionable : ['title', 'body'];
    }

    public function restoreRevision(ContentRevision $revision): void
    {
        $this->fill(Arr::only($revision->data, $this->revisionableAttributes()));
        $this->save();
    }
}
