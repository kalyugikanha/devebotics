<?php

namespace App\Http\Controllers;

use App\Support\Art\CoverArt;
use App\Support\IconRegistry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Serves generated brand artwork as cacheable SVG images. Output depends only
 * on the URL, so responses are cached for a year (the "v" parameter busts
 * caches when the generator changes). Registered without session middleware.
 */
class ArtController extends Controller
{
    public function __invoke(Request $request, string $seed, CoverArt $art): Response
    {
        $icon = $request->query('i');
        $label = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', (string) $request->query('l')) ?? '', 0, 2));

        $svg = $art->svg($seed, IconRegistry::isValid(is_string($icon) ? $icon : null) ? $icon : null, $label);
        $etag = '"'.md5($svg).'"';

        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304, ['ETag' => $etag]);
        }

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => $etag,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
        ]);
    }
}
