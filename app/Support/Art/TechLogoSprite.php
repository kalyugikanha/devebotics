<?php

namespace App\Support\Art;

use Illuminate\Support\HtmlString;

/**
 * Renders bundled technology logos as an inline sprite: the first use of a logo
 * on a page defines a <symbol>, later uses only reference it. A page that shows
 * the same logo several times (hero, marquee, category grid) sends its path once.
 * State is tied to the current request, so every response defines what it uses.
 */
final class TechLogoSprite
{
    /**
     * @var array<string, true>
     */
    private array $defined = [];

    private ?int $requestId = null;

    public function render(string $file, string $class = ''): HtmlString
    {
        if ($this->requestId !== ($current = spl_object_id(request()))) {
            $this->requestId = $current;
            $this->defined = [];
        }

        $id = 'tl-'.$file;
        $open = '<svg class="'.e($class).'" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">';
        $use = '<use href="#'.$id.'"/>';

        if (isset($this->defined[$file])) {
            return new HtmlString($open.$use.'</svg>');
        }

        $path = self::path($file);

        if ($path === null) {
            return new HtmlString('');
        }

        $this->defined[$file] = true;

        return new HtmlString($open.'<symbol id="'.$id.'" viewBox="0 0 24 24"><path d="'.$path.'"/></symbol>'.$use.'</svg>');
    }

    private static function path(string $file): ?string
    {
        if (preg_match('/^[a-z0-9]+$/', $file) !== 1) {
            return null;
        }

        $svg = @file_get_contents(resource_path("svg/tech/{$file}.svg"));

        return $svg !== false && preg_match('/<path d="([MmLlHhVvCcSsQqTtAaZz0-9.,\-\s eE]+)"\/>/', $svg, $matches) === 1 ? $matches[1] : null;
    }
}
