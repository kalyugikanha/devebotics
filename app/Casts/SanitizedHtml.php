<?php

namespace App\Casts;

use App\Support\Html\ContentSanitizer;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Sanitizes rich-text HTML whenever it is written to the model, so every
 * write path (admin forms, seeders, imports, API) is covered.
 *
 * @implements CastsAttributes<string|null, string|null>
 */
class SanitizedHtml implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return $value;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $clean = app(ContentSanitizer::class)->clean((string) $value);

        return $clean === '' ? null : $clean;
    }
}
