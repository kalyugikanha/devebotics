<?php

namespace App\Support;

/**
 * Normalises administrator-entered link targets. Only site-relative paths
 * ("/contact", "#section") and http(s)/mailto/tel URLs are allowed, so
 * "javascript:" or "data:" links can never be rendered.
 */
final class SafeUrl
{
    public static function make(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return url($url);
        }

        if (str_starts_with($url, '#')) {
            return $url;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true) ? $url : null;
    }

    public static function isExternal(?string $url): bool
    {
        $url = self::make($url);

        return $url !== null && str_starts_with($url, 'http') && parse_url($url, PHP_URL_HOST) !== parse_url(url('/'), PHP_URL_HOST);
    }
}
