<?php

namespace App\Support\Art;

use App\Enums\TechnologyCategory;
use Illuminate\Support\Str;

/**
 * Bundled technology logos (resources/svg/tech, see LICENSES.md there) matched
 * to technology slugs. Technologies without a bundled logo get an icon (for
 * generic capabilities such as "Vector databases") or a neutral initials tile,
 * never an imitation of a brand logo.
 */
final class TechLogoRegistry
{
    /**
     * Logo file => brand colour.
     *
     * @var array<string, string>
     */
    private const LOGOS = [
        'laravel' => '#FF2D20', 'nodedotjs' => '#5FA04E', 'python' => '#3776AB', 'rabbitmq' => '#FF6600',
        'react' => '#61DAFB', 'nextdotjs' => '#000000', 'tailwindcss' => '#06B6D4', 'storybook' => '#FF4785',
        'flutter' => '#02569B', 'swift' => '#F05138', 'kotlin' => '#7F52FF', 'firebase' => '#DD2C00',
        'tensorflow' => '#FF6F00', 'pytorch' => '#EE4C2C', 'scikitlearn' => '#F7931E', 'langchain' => '#7FC8FF',
        'googlecloud' => '#4285F4', 'cloudflare' => '#F38020', 'mysql' => '#4479A1', 'postgresql' => '#4169E1',
        'redis' => '#FF4438', 'googlebigquery' => '#669DF6', 'docker' => '#2496ED', 'kubernetes' => '#326CE5',
        'terraform' => '#844FBA', 'githubactions' => '#2088FF', 'gitlab' => '#FC6D26', 'grafana' => '#F46800',
        'linux' => '#FCC624', 'figma' => '#F24E1E', 'stripe' => '#635BFF', 'shopify' => '#7AB55C',
        'woocommerce' => '#96588A', 'n8n' => '#EA4B71', 'odoo' => '#714B67', 'hubspot' => '#FF7A59',
        'owasp' => '#000000', 'typescript' => '#3178C6', 'javascript' => '#F7DF1E', 'mongodb' => '#47A248',
        'graphql' => '#E10098', 'nginx' => '#009639', 'ansible' => '#EE0000', 'elasticsearch' => '#005571',
        'apachekafka' => '#231F20', 'anthropic' => '#191919', 'huggingface' => '#FFD21E', 'wordpress' => '#21759B',
        'django' => '#092E20', 'fastapi' => '#009688', 'dotnet' => '#512BD4', 'go' => '#00ADD8',
        'supabase' => '#3FCF8E', 'vercel' => '#000000', 'openjdk' => '#000000', 'apple' => '#000000',
    ];

    /**
     * Technology slugs whose logo file has a different name.
     *
     * @var array<string, string>
     */
    private const ALIASES = [
        'nodejs' => 'nodedotjs', 'node-js' => 'nodedotjs', 'nextjs' => 'nextdotjs', 'next-js' => 'nextdotjs',
        'tailwind-css' => 'tailwindcss', 'tailwind' => 'tailwindcss', 'react-native' => 'react', 'reactjs' => 'react',
        'scikit-learn' => 'scikitlearn', 'google-cloud' => 'googlecloud', 'gcp' => 'googlecloud',
        'bigquery' => 'googlebigquery', 'github-actions' => 'githubactions', 'gitlab-ci' => 'gitlab',
        'apache-kafka' => 'apachekafka', 'kafka' => 'apachekafka', 'net' => 'dotnet', 'dot-net' => 'dotnet',
        'golang' => 'go', 'java' => 'openjdk', 'ios' => 'apple', 'k8s' => 'kubernetes', 'postgres' => 'postgresql',
        'hugging-face' => 'huggingface', 'claude' => 'anthropic',
    ];

    /**
     * Generic capabilities shown with an icon instead of a brand logo.
     *
     * @var array<string, string>
     */
    private const ICONS = [
        'large-language-models' => 'sparkles', 'vector-databases' => 'circle-stack', 'cloud-platforms' => 'cloud',
        'ai-services' => 'cpu-chip', 'data-platforms' => 'server-stack', 'low-code-tools' => 'puzzle-piece',
    ];

    /**
     * Readable short labels for well-known technologies without a bundled logo.
     *
     * @var array<string, string>
     */
    private const INITIALS = [
        'php' => 'php', 'vuejs' => 'Vue', 'aws' => 'AWS', 'microsoft-azure' => 'Az', 'dbt' => 'dbt', 'power-bi' => 'BI',
        'windows-server' => 'WS', 'power-automate' => 'PA', 'salesforce' => 'SF', 'microsoft-dynamics-365' => 'D365',
        'microsoft-365' => 'M365', 'openai' => 'AI',
    ];

    /**
     * Initials-tile colour per category.
     *
     * @var array<string, string>
     */
    private const CATEGORY_COLOURS = [
        'frontend' => '#2563EB', 'backend' => '#4F46E5', 'mobile' => '#0E7490', 'ai_ml' => '#7C3AED',
        'cloud' => '#0369A1', 'database' => '#0F766E', 'devops' => '#334155', 'design' => '#9333EA', 'other' => '#475569',
    ];

    /**
     * @return array<string, string> logo file => label, for admin pickers
     */
    public static function options(): array
    {
        return collect(self::LOGOS)->keys()
            ->mapWithKeys(fn (string $file) => [$file => Str::headline(str_replace('dot', '.', $file))])
            ->all();
    }

    /**
     * How to draw a technology: a bundled logo, an icon, or an initials tile.
     *
     * @return array{type: 'logo'|'icon'|'initials', name: string, color: string, dark: string}
     */
    public static function resolve(string $slug, string $name, TechnologyCategory|string|null $category = null): array
    {
        $slug = Str::slug($slug);
        $file = self::ALIASES[$slug] ?? $slug;

        if (isset(self::LOGOS[$file])) {
            $color = self::LOGOS[$file];

            return ['type' => 'logo', 'name' => $file, 'color' => self::forLight($color), 'dark' => self::forDark($color)];
        }

        $category = $category instanceof TechnologyCategory ? $category->value : (string) $category;
        $color = self::CATEGORY_COLOURS[$category] ?? self::CATEGORY_COLOURS['other'];

        if (isset(self::ICONS[$slug])) {
            return ['type' => 'icon', 'name' => self::ICONS[$slug], 'color' => $color, 'dark' => self::forDark($color)];
        }

        return ['type' => 'initials', 'name' => self::INITIALS[$slug] ?? self::initials($name), 'color' => $color, 'dark' => self::forDark($color)];
    }

    public static function initials(string $name): string
    {
        $words = preg_split('/[\s\-\.]+/', trim(preg_replace('/[^\pL\pN\s\-\.]/u', '', $name) ?? '')) ?: [];
        $words = array_values(array_filter($words, fn (string $word) => ! in_array(Str::lower($word), ['microsoft', 'the', 'and'], true) || count($words) === 1));

        $initials = count($words) > 1
            ? mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1)
            : mb_substr($words[0] ?? '?', 0, 2);

        return Str::upper($initials);
    }

    /**
     * Very light brand colours (yellows) are darkened slightly so they stay visible on white.
     */
    private static function forLight(string $hex): string
    {
        return self::luminance($hex) > 0.55 ? self::mix($hex, '#000000', 0.28) : $hex;
    }

    /**
     * Near-black brand colours turn light on dark backgrounds.
     */
    private static function forDark(string $hex): string
    {
        return self::luminance($hex) < 0.08 ? '#E9EEF9' : self::mix($hex, '#FFFFFF', 0.12);
    }

    private static function luminance(string $hex): float
    {
        [$r, $g, $b] = array_map(function (int $channel): float {
            $c = $channel / 255;

            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        }, sscanf(ltrim($hex, '#'), '%02x%02x%02x'));

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    private static function mix(string $hex, string $with, float $amount): string
    {
        $a = sscanf(ltrim($hex, '#'), '%02x%02x%02x');
        $b = sscanf(ltrim($with, '#'), '%02x%02x%02x');

        return sprintf('#%02X%02X%02X', ...array_map(fn (int $x, int $y) => (int) round($x + ($y - $x) * $amount), $a, $b));
    }
}
