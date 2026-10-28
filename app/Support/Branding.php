<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Facades\Cache;

/**
 * Logo, mark and favicon URLs. Administrator uploads (Settings › Company)
 * take precedence over the bundled brand assets in public/images/brand.
 */
class Branding
{
    public static function markUrl(): string
    {
        return self::mediaUrl(settings('company.logo_mark_media_id')) ?? asset('images/brand/softec-mark.webp');
    }

    public static function logoLightUrl(): ?string
    {
        return self::mediaUrl(settings('company.logo_light_media_id'));
    }

    public static function logoDarkUrl(): ?string
    {
        return self::mediaUrl(settings('company.logo_dark_media_id')) ?? self::logoLightUrl();
    }

    /**
     * Full logo for schema.org Organization markup (crawlable, at least 112×112).
     */
    public static function organizationLogoUrl(): string
    {
        return self::logoLightUrl() ?? asset('images/brand/softec-logo-full.png');
    }

    public static function faviconUrl(): string
    {
        return self::mediaUrl(settings('company.favicon_media_id')) ?? asset('images/brand/favicon-32.png');
    }

    public static function defaultOgImageUrl(): string
    {
        return self::mediaUrl(settings('seo.default_og_image_media_id')) ?? asset('images/brand/og-default.jpg');
    }

    public static function showsWordmark(): bool
    {
        return (bool) settings('company.show_wordmark', true) || self::logoLightUrl() === null;
    }

    public static function mediaUrl(mixed $mediaId): ?string
    {
        if (blank($mediaId)) {
            return null;
        }

        return Cache::remember('branding.media.'.$mediaId, now()->addHour(), fn (): ?string => Media::query()->find($mediaId)?->url());
    }
}
