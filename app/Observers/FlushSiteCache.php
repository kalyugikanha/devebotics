<?php

namespace App\Observers;

use App\Http\Controllers\SitemapController;
use App\Support\SiteNavigation;
use Illuminate\Support\Facades\Cache;

/**
 * Clears cached navigation and sitemaps when public content changes.
 */
class FlushSiteCache
{
    public function saved(): void
    {
        $this->flush();
    }

    public function deleted(): void
    {
        $this->flush();
    }

    public function restored(): void
    {
        $this->flush();
    }

    private function flush(): void
    {
        SiteNavigation::flush();

        foreach (SitemapController::types() as $type) {
            Cache::forget("sitemap.{$type}");
        }
    }
}
