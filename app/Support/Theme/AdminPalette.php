<?php

namespace App\Support\Theme;

use Filament\Support\Colors\Color;

/**
 * Builds a Filament colour palette in which the exact brand colour is shade
 * 600 (the shade Filament uses for primary buttons), so admin buttons match
 * the website instead of falling back to a pastel tint.
 */
final class AdminPalette
{
    /**
     * @return array<int, string>
     */
    public static function fromHex(string $hex): array
    {
        $generated = Color::hex($hex);

        if (! Color::isTextContrastRatioAccessible('#ffffff', $hex)) {
            // Light brand colours keep Filament's generated palette (dark text is used automatically).
            return $generated;
        }

        [$lightness, $chroma, $hue] = self::parse(Color::convertToOklch($hex));

        $palette = $generated;
        $palette[600] = sprintf('oklch(%.4f %.4f %.3f)', $lightness, $chroma, $hue);

        foreach ([700 => 0.88, 800 => 0.76, 900 => 0.66, 950 => 0.5] as $shade => $factor) {
            $palette[$shade] = sprintf('oklch(%.4f %.4f %.3f)', $lightness * $factor, $chroma * min(1, $factor + 0.1), $hue);
        }

        // Shade 500 is the hover colour of primary buttons: keep it as light as possible while white
        // text still passes AA, otherwise Filament falls back to a pastel button with dark text.
        foreach ([1.12, 1.09, 1.06, 1.03] as $factor) {
            $hover = sprintf('oklch(%.4f %.4f %.3f)', min(0.92, $lightness * $factor), $chroma, $hue);

            if (Color::isTextContrastRatioAccessible('#ffffff', $hover)) {
                $palette[500] = $hover;

                break;
            }
        }

        // Keep lighter shades lighter than the brand colour.
        foreach ([500 => 1.12, 400 => 1.3] as $shade => $factor) {
            [$generatedLightness] = self::parse($palette[$shade]);

            if ($generatedLightness <= $lightness) {
                $palette[$shade] = sprintf('oklch(%.4f %.4f %.3f)', min(0.92, $lightness * $factor), $chroma * 0.9, $hue);
            }
        }

        ksort($palette);

        return $palette;
    }

    /**
     * @return array{0: float, 1: float, 2: float}
     */
    private static function parse(string $oklch): array
    {
        preg_match('/oklch\(\s*([\d.]+)\s+([\d.]+)\s+([\d.]+)/', $oklch, $matches);

        return [(float) ($matches[1] ?? 0.5), (float) ($matches[2] ?? 0.1), (float) ($matches[3] ?? 290)];
    }
}
