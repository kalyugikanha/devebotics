<?php

namespace App\Support\Art;

use App\Support\IconRegistry;
use Illuminate\Support\Str;

/**
 * Procedural, deterministic SVG artwork in the Softec brand style (orbit
 * rings, pixel squares, gradient ribbon, glass icon tile). Used whenever a
 * service, industry, product, case study or article has no uploaded image,
 * so every card has an original visual without stock photography.
 *
 * The same seed always produces the same composition.
 */
class CoverArt
{
    /**
     * Palettes taken from the logo gradients.
     *
     * @var array<int, array{0: string, 1: string, 2: string}>
     */
    private const PALETTES = [
        ['#7C3AED', '#EC4899', '#F59E0B'],
        ['#2563EB', '#22D3EE', '#34D399'],
        ['#DB2777', '#F97316', '#FACC15'],
        ['#4F46E5', '#8B5CF6', '#22D3EE'],
        ['#0EA5E9', '#6366F1', '#EC4899'],
        ['#059669', '#22D3EE', '#3B82F6'],
        ['#9333EA', '#3B82F6', '#34D399'],
    ];

    /**
     * Bump when the generator output changes so cached /art URLs refresh.
     */
    public const VERSION = 1;

    private int $state;

    private static int $instances = 0;

    public function svg(string $seed, ?string $icon = null, string $label = '', int $width = 640, int $height = 400): string
    {
        $this->state = crc32($seed) ?: 1;
        $id = 'art'.substr(md5($seed), 0, 6).(++self::$instances);
        [$c1, $c2, $c3] = self::PALETTES[$this->int(0, count(self::PALETTES) - 1)];

        $cx = $width * $this->float(0.42, 0.62);
        $cy = $height * $this->float(0.42, 0.58);
        $angle = $this->int(-32, -14);

        $orbits = '';

        foreach ([[1.0, 0.36, 1.6], [0.78, 0.26, 1.1], [1.25, 0.46, 1.0]] as $index => [$scaleX, $scaleY, $stroke]) {
            $rx = $width * 0.38 * $scaleX * $this->float(0.9, 1.1);
            $ry = $height * $scaleY * $this->float(0.85, 1.1);
            $opacity = [0.55, 0.32, 0.18][$index];
            $orbits .= sprintf(
                '<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="none" stroke="%s" stroke-opacity="%.2f" stroke-width="%.1f" transform="rotate(%d %.1f %.1f)"/>',
                $cx, $cy, $rx, $ry, $index === 0 ? "url(#{$id}-orbit)" : '#fff', $opacity, $stroke, $angle, $cx, $cy,
            );
        }

        // Sphere riding on the main orbit.
        $theta = deg2rad($this->int(-60, 20));
        $rx = $width * 0.38;
        $ry = $height * 0.36;
        $sx = $cx + $rx * cos($theta) * cos(deg2rad($angle)) - $ry * sin($theta) * sin(deg2rad($angle));
        $sy = $cy + $rx * cos($theta) * sin(deg2rad($angle)) + $ry * sin($theta) * cos(deg2rad($angle));
        $sphere = sprintf('<circle cx="%.1f" cy="%.1f" r="%d" fill="url(#%s-sphere)"/><circle cx="%.1f" cy="%.1f" r="%d" fill="none" stroke="#fff" stroke-opacity=".35" stroke-width="1.5"/>', $sx, $sy, $this->int(12, 18), $id, $sx, $sy, $this->int(24, 30));

        // Pixel cluster (echoes the squares in the logo).
        $px = $width * $this->float(0.05, 0.12);
        $py = $height * $this->float(0.58, 0.66);
        $pixels = '';
        $grid = [[0, 1], [1, 0], [1, 1], [2, 0], [0, 2], [2, 2], [1, 2]];
        $count = $this->int(4, 6);

        for ($i = 0; $i < $count; $i++) {
            [$gx, $gy] = $grid[$i];
            $size = $this->int(16, 26);
            $pixels .= sprintf('<rect x="%.1f" y="%.1f" width="%d" height="%d" rx="%d" fill="#fff" fill-opacity="%.2f"/>', $px + $gx * 34, $py + $gy * 34, $size, $size, (int) round($size / 3.2), $this->float(0.18, 0.55));
        }

        // Ribbon swoosh.
        $ribbon = sprintf(
            '<path d="M%.1f %.1f C %.1f %.1f, %.1f %.1f, %.1f %.1f" fill="none" stroke="url(#%s-ribbon)" stroke-width="%d" stroke-linecap="round" stroke-opacity=".55"/>',
            -20, $height * $this->float(0.7, 0.9),
            $width * 0.3, $height * $this->float(0.2, 0.5),
            $width * 0.6, $height * $this->float(0.9, 1.2),
            $width + 20, $height * $this->float(0.3, 0.55),
            $id, $this->int(22, 34),
        );

        // Glass tile with the icon.
        $tile = 112;
        $tx = $cx - $tile / 2;
        $ty = $cy - $tile / 2;
        $iconMarkup = '';

        if ($icon !== null || $label === '') {
            $inner = svg(IconRegistry::component($icon))->contents();
            $iconMarkup = sprintf('<svg x="%.1f" y="%.1f" width="56" height="56" viewBox="0 0 24 24" fill="none" color="#fff" stroke="#fff" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">%s</svg>', $tx + 28, $ty + 28, $inner);
        } else {
            $iconMarkup = sprintf('<text x="%.1f" y="%.1f" text-anchor="middle" font-family="Plus Jakarta Sans Variable, Inter, sans-serif" font-size="40" font-weight="800" fill="#fff">%s</text>', $cx, $cy + 14, e(Str::upper(Str::substr($label, 0, 2))));
        }

        $glass = sprintf('<rect x="%.1f" y="%.1f" width="%d" height="%d" rx="30" fill="#fff" fill-opacity=".16" stroke="#fff" stroke-opacity=".45"/>', $tx, $ty, $tile, $tile).$iconMarkup;

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %2$d" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false" class="h-full w-full">'
            .'<defs>'
            .'<linearGradient id="%3$s-bg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="%4$s"/><stop offset="1" stop-color="%5$s"/></linearGradient>'
            .'<radialGradient id="%3$s-glow" cx=".8" cy=".15" r=".7"><stop offset="0" stop-color="%6$s" stop-opacity=".85"/><stop offset="1" stop-color="%6$s" stop-opacity="0"/></radialGradient>'
            .'<radialGradient id="%3$s-shade" cx=".3" cy="1" r=".9"><stop offset="0" stop-color="#0b1020" stop-opacity=".45"/><stop offset="1" stop-color="#0b1020" stop-opacity="0"/></radialGradient>'
            .'<radialGradient id="%3$s-sphere" cx=".35" cy=".35" r=".75"><stop offset="0" stop-color="#fff"/><stop offset=".45" stop-color="%6$s"/><stop offset="1" stop-color="%4$s"/></radialGradient>'
            .'<linearGradient id="%3$s-orbit" x1="0" x2="1"><stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff"/><stop offset="1" stop-color="#fff" stop-opacity=".2"/></linearGradient>'
            .'<linearGradient id="%3$s-ribbon" x1="0" x2="1"><stop offset="0" stop-color="%6$s" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".6"/><stop offset="1" stop-color="%6$s" stop-opacity=".1"/></linearGradient>'
            .'<pattern id="%3$s-dots" width="22" height="22" patternUnits="userSpaceOnUse"><circle cx="1.5" cy="1.5" r="1.3" fill="#fff" fill-opacity=".16"/></pattern>'
            .'</defs>'
            .'<rect width="100%%" height="100%%" fill="url(#%3$s-bg)"/>'
            .'<rect width="100%%" height="100%%" fill="url(#%3$s-glow)"/>'
            .'<rect width="100%%" height="100%%" fill="url(#%3$s-dots)"/>'
            .'%7$s%8$s%9$s%10$s%11$s'
            .'<rect width="100%%" height="100%%" fill="url(#%3$s-shade)"/>'
            .'</svg>',
            $width, $height, $id, $c1, $c2, $c3, $ribbon, $orbits, $pixels, $sphere, $glass,
        );
    }

    private function next(): int
    {
        // xorshift32 — deterministic, fast, good enough for layout jitter.
        $x = $this->state;
        $x ^= ($x << 13) & 0xFFFFFFFF;
        $x ^= ($x >> 17);
        $x ^= ($x << 5) & 0xFFFFFFFF;
        $this->state = $x & 0xFFFFFFFF;

        return $this->state;
    }

    private function int(int $min, int $max): int
    {
        return $min + ($this->next() % ($max - $min + 1));
    }

    private function float(float $min, float $max): float
    {
        return $min + ($this->next() / 0xFFFFFFFF) * ($max - $min);
    }
}
