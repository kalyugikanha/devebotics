<?php

namespace App\Http\Controllers;

use App\Support\Seo\SeoData;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        if (! SeoData::siteAllowsIndexing()) {
            // Staging, local or indexing switched off: block all crawlers.
            return response("User-agent: *\nDisallow: /\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $disallow = array_merge(
            ['/admin', '/livewire', '/search', '/preview', '/theme-preview', '/feedback/verify', '/newsletter/', '/cookie-consent'],
            array_filter(array_map(fn ($path) => '/'.ltrim(trim((string) $path), '/'), (array) settings('seo.robots_disallow', []))),
        );

        $lines = ['User-agent: *', 'Allow: /'];

        foreach (array_unique($disallow) as $path) {
            $lines[] = 'Disallow: '.$path;
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.route('sitemap');

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
