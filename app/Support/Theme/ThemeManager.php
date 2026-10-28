<?php

namespace App\Support\Theme;

use App\Enums\ThemeMode;
use App\Support\Settings\Settings;
use App\Support\Settings\SettingsRegistry;
use Illuminate\Support\Facades\Auth;

/**
 * Resolves the active theme (published, or the draft while an authorised
 * administrator is previewing) and renders it as CSS custom properties.
 */
class ThemeManager
{
    public const PREVIEW_SESSION_KEY = 'theme_preview';

    public const COLOR_KEYS = ['primary', 'secondary', 'accent', 'highlight', 'background', 'surface', 'foreground', 'muted', 'border'];

    public const RADIUS_PRESETS = [
        'sharp' => ['sm' => '0.25rem', 'md' => '0.375rem', 'lg' => '0.5rem', 'xl' => '0.75rem'],
        'soft' => ['sm' => '0.375rem', 'md' => '0.625rem', 'lg' => '0.875rem', 'xl' => '1.25rem'],
        'rounded' => ['sm' => '0.5rem', 'md' => '0.875rem', 'lg' => '1.25rem', 'xl' => '1.75rem'],
    ];

    public function __construct(private Settings $settings) {}

    /**
     * @return array<string, mixed>
     */
    public function active(): array
    {
        return $this->normalize($this->isPreviewing() ? $this->draft() : $this->published());
    }

    /**
     * @return array<string, mixed>
     */
    public function published(): array
    {
        return $this->normalize($this->settings->get('theme.published', SettingsRegistry::defaultTheme()));
    }

    /**
     * @return array<string, mixed>
     */
    public function draft(): array
    {
        return $this->normalize($this->settings->get('theme.draft', $this->published()));
    }

    public function isPreviewing(): bool
    {
        return (bool) session(self::PREVIEW_SESSION_KEY, false)
            && Auth::check()
            && Auth::user()->can('appearance.manage');
    }

    public function defaultMode(): ThemeMode
    {
        return ThemeMode::tryFrom((string) ($this->active()['default_mode'] ?? '')) ?? ThemeMode::System;
    }

    public function allowsSwitcher(): bool
    {
        return (bool) ($this->active()['allow_switcher'] ?? true);
    }

    /**
     * CSS custom properties for both colour schemes. Values are validated hex
     * colours, so the output is safe to inline in a <style> element.
     *
     * @param  array<string, mixed>|null  $theme
     */
    public function css(?array $theme = null): string
    {
        $theme = $this->normalize($theme ?? $this->active());
        $radius = self::RADIUS_PRESETS[$theme['radius']] ?? self::RADIUS_PRESETS['rounded'];

        $radiusVars = collect($radius)->map(fn (string $value, string $key) => "--radius-brand-{$key}:{$value}")->implode(';');

        return ':root{'.$this->vars($theme['light']).';'.$radiusVars.';color-scheme:light}'
            .'.dark{'.$this->vars($theme['dark']).';color-scheme:dark}';
    }

    /**
     * Contrast warnings (WCAG 2.2 AA) for an administrator-defined theme.
     *
     * @param  array<string, mixed>  $theme
     * @return array<int, string>
     */
    public function contrastWarnings(array $theme): array
    {
        $theme = $this->normalize($theme);
        $warnings = [];

        foreach (['light', 'dark'] as $scheme) {
            $palette = $theme[$scheme];
            $checks = [
                'Body text on background' => [$palette['foreground'], $palette['background'], 4.5],
                'Body text on surface' => [$palette['foreground'], $palette['surface'], 4.5],
                'Muted text on background' => [$palette['muted'], $palette['background'], 4.5],
                'Primary on background (links)' => [$palette['primary'], $palette['background'], 4.5],
                'Button text on primary' => [$scheme === 'light' ? '#FFFFFF' : $palette['background'], $palette['primary'], 4.5],
            ];

            foreach ($checks as $label => [$fg, $bg, $minimum]) {
                $ratio = self::contrastRatio($fg, $bg);

                if ($ratio < $minimum) {
                    $warnings[] = ucfirst($scheme).' mode — '.$label.': '.number_format($ratio, 2).':1 (needs '.$minimum.':1)';
                }
            }
        }

        return $warnings;
    }

    public static function contrastRatio(string $foreground, string $background): float
    {
        $l1 = self::luminance($foreground);
        $l2 = self::luminance($background);

        return (max($l1, $l2) + 0.05) / (min($l1, $l2) + 0.05);
    }

    public static function isHexColor(mixed $value): bool
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1;
    }

    /**
     * Fill gaps with defaults and discard anything that is not a valid value.
     *
     * @param  mixed  $theme
     * @return array<string, mixed>
     */
    public function normalize($theme): array
    {
        $defaults = SettingsRegistry::defaultTheme();
        $theme = is_array($theme) ? $theme : [];

        if (($theme['default_mode'] ?? null) instanceof \BackedEnum) {
            $theme['default_mode'] = $theme['default_mode']->value;
        }

        $normalized = [
            'default_mode' => ThemeMode::tryFrom((string) ($theme['default_mode'] ?? ''))?->value ?? $defaults['default_mode'],
            'allow_switcher' => (bool) ($theme['allow_switcher'] ?? $defaults['allow_switcher']),
            'radius' => array_key_exists((string) ($theme['radius'] ?? ''), self::RADIUS_PRESETS) ? $theme['radius'] : $defaults['radius'],
        ];

        foreach (['light', 'dark'] as $scheme) {
            foreach (self::COLOR_KEYS as $key) {
                $value = $theme[$scheme][$key] ?? null;
                $normalized[$scheme][$key] = self::isHexColor($value) ? strtoupper($value) : $defaults[$scheme][$key];
            }
        }

        return $normalized;
    }

    /**
     * @param  array<string, string>  $palette
     */
    private function vars(array $palette): string
    {
        return collect($palette)->map(fn (string $hex, string $key) => "--brand-{$key}:{$hex}")->implode(';');
    }

    private static function luminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        $channels = array_map(fn (string $c) => hexdec($c) / 255, str_split($hex, 2));

        [$r, $g, $b] = array_map(
            fn (float $c) => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4,
            $channels,
        );

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
}
