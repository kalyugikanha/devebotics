<?php

namespace App\Support\Html;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Allow-list HTML sanitizer for rich-text content written in the admin panel.
 *
 * Strips scripts, event handlers, inline styles, iframes, forms, `javascript:`
 * and `data:` URLs. Top-level <h1> is unwrapped because every page already
 * renders its own H1 (keeps a correct heading hierarchy for SEO/a11y).
 */
class ContentSanitizer
{
    private ?HtmlSanitizer $sanitizer = null;

    public function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        return trim($this->sanitizer()->sanitize($html));
    }

    private function sanitizer(): HtmlSanitizer
    {
        if ($this->sanitizer !== null) {
            return $this->sanitizer;
        }

        $config = (new HtmlSanitizerConfig)
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->allowRelativeLinks()
            ->allowMediaSchemes(['https', 'http'])
            ->allowRelativeMedias()
            ->blockElement('h1')
            ->blockElement('div')
            ->blockElement('span')
            ->forceAttribute('a', 'rel', 'noopener noreferrer')
            ->withMaxInputLength(500_000);

        foreach (['p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup', 'mark', 'small', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'li', 'blockquote', 'pre', 'code', 'hr', 'figure', 'figcaption', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'caption'] as $element) {
            $config = $config->allowElement($element);
        }

        $config = $config
            ->allowElement('a', ['href', 'title', 'target'])
            ->allowElement('ol', ['start'])
            ->allowElement('img', ['src', 'alt', 'title', 'width', 'height'])
            ->allowElement('th', ['colspan', 'rowspan', 'scope'])
            ->allowElement('td', ['colspan', 'rowspan']);

        return $this->sanitizer = new HtmlSanitizer($config);
    }
}
