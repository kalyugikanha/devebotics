<?php

namespace App\Contracts;

/**
 * Content that is reachable on the public website.
 */
interface HasPublicUrl
{
    public function publicUrl(): string;

    /**
     * Relative public path for a given slug — used to create redirects
     * automatically when the slug of published content changes.
     */
    public function publicPathForSlug(string $slug): string;

    /**
     * Whether the record is currently visible to anonymous visitors.
     */
    public function isPubliclyVisible(): bool;
}
