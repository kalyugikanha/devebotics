<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['from_path', 'to_url', 'status_code', 'is_active', 'is_automatic', 'note', 'created_by'])]
class Redirect extends Model
{
    use Auditable;

    public const CACHE_KEY = 'redirects.map';

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'is_active' => 'boolean',
            'is_automatic' => 'boolean',
            'last_hit_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Redirect $redirect): void {
            $redirect->from_path = static::normalizePath($redirect->from_path);
        });

        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function normalizePath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';

        return '/'.trim($path, '/');
    }

    /**
     * Create (or refresh) an automatic 301 and collapse redirect chains.
     */
    public static function recordAutomatic(string $from, string $to, string $note): void
    {
        $from = static::normalizePath($from);
        $to = static::normalizePath($to);

        if ($from === $to) {
            return;
        }

        // A → B now becomes B → C: point existing redirects to the final destination.
        static::query()->where('to_url', $from)->update(['to_url' => $to]);
        // The new URL is live again; remove any redirect away from it.
        static::query()->where('from_path', $to)->delete();

        static::query()->updateOrCreate(
            ['from_path' => $from],
            ['to_url' => $to, 'status_code' => 301, 'is_active' => true, 'is_automatic' => true, 'note' => $note],
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Cached lookup map: from_path => [to_url, status_code, id].
     *
     * @return array<string, array{0: ?string, 1: int, 2: int}>
     */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()
            ->where('is_active', true)
            ->get(['id', 'from_path', 'to_url', 'status_code'])
            ->mapWithKeys(fn (Redirect $r) => [$r->from_path => [$r->to_url, $r->status_code, $r->id]])
            ->all());
    }
}
