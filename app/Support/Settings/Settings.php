<?php

namespace App\Support\Settings;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Cached access to admin-editable settings with registry defaults.
 *
 *     settings('company.name');
 *     app(Settings::class)->setMany('contact', [...]);
 */
class Settings
{
    public const CACHE_KEY = 'settings.all';

    /**
     * @var array<string, array<string, mixed>>|null
     */
    private ?array $loaded = null;

    public function get(string $key, mixed $default = null): mixed
    {
        $value = data_get($this->all(), $key);

        if ($value === null) {
            // Stored value → registry default → caller default.
            return SettingsRegistry::default($key) ?? $default;
        }

        return $value;
    }

    /**
     * Settings for one group merged over registry defaults.
     *
     * @return array<string, mixed>
     */
    public function group(string $group): array
    {
        return array_replace(SettingsRegistry::defaults()[$group] ?? [], $this->all()[$group] ?? []);
    }

    public function set(string $group, string $key, mixed $value): void
    {
        Setting::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'updated_by' => Auth::id()],
        );

        $this->flush();
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => $value, 'updated_by' => Auth::id()],
            );
        }

        $this->flush();
    }

    public function flush(): void
    {
        $this->loaded = null;
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Stored values only (no defaults), grouped by module.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        if ($this->loaded !== null) {
            return $this->loaded;
        }

        try {
            return $this->loaded = Cache::rememberForever(self::CACHE_KEY, function (): array {
                if (! Schema::hasTable('settings')) {
                    return [];
                }

                $grouped = [];

                foreach (Setting::query()->get(['group', 'key', 'value']) as $setting) {
                    Arr::set($grouped, $setting->group.'.'.$setting->key, $setting->value);
                }

                return $grouped;
            });
        } catch (Throwable) {
            // Database unavailable (e.g. during installation) — fall back to defaults.
            return $this->loaded = [];
        }
    }
}
