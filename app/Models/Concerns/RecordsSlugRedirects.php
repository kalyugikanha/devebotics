<?php

namespace App\Models\Concerns;

use App\Contracts\HasPublicUrl;
use App\Models\Redirect;
use Illuminate\Database\Eloquent\Model;

/**
 * When the slug of publicly visible content changes, a 301 redirect from the
 * old URL to the new one is created automatically so links and rankings
 * are not lost.
 *
 * @mixin Model&HasPublicUrl
 */
trait RecordsSlugRedirects
{
    public static function bootRecordsSlugRedirects(): void
    {
        static::updated(function (Model $model): void {
            /** @var Model&HasPublicUrl $model */
            if (! $model->wasChanged('slug') || blank($model->getOriginal('slug'))) {
                return;
            }

            $original = $model->replicate()->forceFill($model->getOriginal());

            if (! $original->isPubliclyVisible()) {
                return;
            }

            $from = $model->publicPathForSlug($model->getOriginal('slug'));
            $to = $model->publicPathForSlug($model->slug);

            Redirect::recordAutomatic($from, $to, 'Slug changed on '.class_basename($model).' #'.$model->getKey());
        });
    }
}
