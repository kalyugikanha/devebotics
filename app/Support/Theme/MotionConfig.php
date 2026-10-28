<?php

namespace App\Support\Theme;

use App\Support\Settings\Settings;

/**
 * Whitelisted animation configuration exposed to the front-end as JSON.
 * Administrators choose presets; no executable code is ever accepted.
 */
class MotionConfig
{
    public const INTENSITIES = ['subtle' => 'Subtle', 'standard' => 'Standard', 'expressive' => 'Expressive'];

    public const HERO_PRESETS = ['rise' => 'Rise & reveal (word by word)', 'blur' => 'Soft blur-in', 'fade' => 'Simple fade', 'none' => 'No hero animation'];

    public const REVEAL_PRESETS = ['fade-up' => 'Fade up', 'fade' => 'Fade', 'zoom' => 'Gentle zoom', 'slide' => 'Slide in', 'none' => 'No scroll reveal'];

    public function __construct(private Settings $settings) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $s = $this->settings->group('animation');

        return [
            'enabled' => (bool) $s['enabled'],
            'intensity' => array_key_exists((string) $s['intensity'], self::INTENSITIES) ? $s['intensity'] : 'standard',
            'durationScale' => max(0.5, min(1.5, (float) $s['duration_scale'])),
            'hero' => array_key_exists((string) $s['hero_preset'], self::HERO_PRESETS) ? $s['hero_preset'] : 'rise',
            'reveal' => array_key_exists((string) $s['reveal_preset'], self::REVEAL_PRESETS) ? $s['reveal_preset'] : 'fade-up',
            'stagger' => (bool) $s['stagger'],
            'parallax' => (bool) $s['parallax'],
            'counters' => (bool) $s['counters'],
            'ambient' => (bool) $s['ambient'],
            'marquee' => (bool) $s['marquee'],
            'decorativeOnMobile' => (bool) $s['decorative_on_mobile'],
        ];
    }

    public function enabled(): bool
    {
        return $this->toArray()['enabled'];
    }
}
