<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Safe inline emphasis for administrator-entered headings:
 * "Build smarter software with [AI at the core]" renders the bracketed
 * words with the brand gradient. Everything else is HTML-escaped.
 */
final class Typography
{
    public static function highlight(?string $text): HtmlString
    {
        $escaped = e((string) $text);

        return new HtmlString((string) preg_replace('/\[(.+?)\]/u', '<span class="text-gradient">$1</span>', $escaped));
    }

    /**
     * Strip highlight markers for plain-text contexts (meta tags, aria labels).
     */
    public static function plain(?string $text): string
    {
        return (string) preg_replace('/\[(.+?)\]/u', '$1', (string) $text);
    }

    /**
     * Wrap each word in a span for the CSS hero reveal (keeps highlight markers).
     */
    public static function heroWords(?string $text): HtmlString
    {
        $index = 0;
        $parts = preg_split('/(\[.+?\])/u', (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $html = '';

        foreach ($parts as $part) {
            $isHighlight = str_starts_with($part, '[') && str_ends_with($part, ']');
            $words = preg_split('/\s+/u', trim($isHighlight ? substr($part, 1, -1) : $part), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            // background-clip:text does not paint inline-block children, so each highlighted word carries the gradient itself.
            $spans = array_map(function (string $word) use (&$index, $isHighlight): string {
                return '<span class="hero-word'.($isHighlight ? ' text-gradient' : '').'" style="--i:'.$index++.'">'.e($word).'</span>';
            }, $words);

            $html .= ($html !== '' ? ' ' : '').implode(' ', $spans);
        }

        return new HtmlString($html);
    }
}
