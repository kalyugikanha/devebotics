<?php

namespace App\Models\Concerns;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Model
 */
trait HasSeo
{
    public static function bootHasSeo(): void
    {
        static::forceDeleted(function (Model $model): void {
            $model->seo()->delete();
        });
    }

    /**
     * @return MorphOne<SeoMeta, $this>
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Description used when no explicit meta description is set.
     */
    public function seoFallbackDescription(): ?string
    {
        foreach (['excerpt', 'summary', 'tagline', 'overview'] as $column) {
            if (filled($this->getAttribute($column))) {
                return (string) $this->getAttribute($column);
            }
        }

        return null;
    }
}
