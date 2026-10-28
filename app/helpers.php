<?php

use App\Support\Settings\Settings;

if (! function_exists('settings')) {
    /**
     * Read an admin-editable setting ("group.key") with registry defaults.
     */
    function settings(?string $key = null, mixed $default = null): mixed
    {
        $settings = app(Settings::class);

        return $key === null ? $settings : $settings->get($key, $default);
    }
}
