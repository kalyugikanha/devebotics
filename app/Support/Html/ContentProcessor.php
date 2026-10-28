<?php

namespace App\Support\Html;

use Dom\Element;
use Dom\HTMLDocument;
use Illuminate\Support\Str;

/**
 * Render-time enhancements for already-sanitised rich text:
 * heading anchors + table of contents, lazy images, safe external links
 * and horizontally scrollable tables.
 */
class ContentProcessor
{
    /**
     * @return array{html: string, toc: array<int, array{id: string, text: string, level: int}>}
     */
    public function process(?string $html, bool $buildToc = true): array
    {
        if ($html === null || trim($html) === '') {
            return ['html' => '', 'toc' => []];
        }

        $document = HTMLDocument::createFromString('<!DOCTYPE html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);
        $body = $document->body;

        if ($body === null) {
            return ['html' => $html, 'toc' => []];
        }

        $toc = [];
        $used = [];

        foreach ($body->querySelectorAll('h2, h3') as $heading) {
            /** @var Element $heading */
            $text = trim((string) $heading->textContent);

            if ($text === '') {
                continue;
            }

            $id = Str::slug(Str::limit($text, 60, '')) ?: 'section';
            $base = $id;
            $suffix = 2;

            while (isset($used[$id])) {
                $id = $base.'-'.$suffix++;
            }

            $used[$id] = true;
            $heading->setAttribute('id', $id);

            if ($buildToc) {
                $toc[] = ['id' => $id, 'text' => $text, 'level' => $heading->tagName === 'H3' ? 3 : 2];
            }
        }

        foreach ($body->querySelectorAll('img') as $image) {
            /** @var Element $image */
            $image->setAttribute('loading', 'lazy');
            $image->setAttribute('decoding', 'async');

            if (! $image->hasAttribute('alt')) {
                $image->setAttribute('alt', '');
            }
        }

        $host = parse_url(url('/'), PHP_URL_HOST);

        foreach ($body->querySelectorAll('a[href]') as $link) {
            /** @var Element $link */
            $linkHost = parse_url((string) $link->getAttribute('href'), PHP_URL_HOST);

            if ($linkHost !== null && $linkHost !== $host) {
                $link->setAttribute('target', '_blank');
                $link->setAttribute('rel', 'noopener noreferrer');
            } else {
                $link->removeAttribute('target');
                $link->removeAttribute('rel');
            }
        }

        foreach ($body->querySelectorAll('table') as $table) {
            /** @var Element $table */
            $wrapper = $document->createElement('div');
            $wrapper->setAttribute('class', 'table-scroll');
            $wrapper->setAttribute('tabindex', '0');
            $table->parentNode?->insertBefore($wrapper, $table);
            $wrapper->appendChild($table);
        }

        return ['html' => $body->innerHTML, 'toc' => count($toc) >= 2 ? $toc : []];
    }
}
