<?php

namespace App\Support\Art;

use App\Support\IconRegistry;

/**
 * Whitelist of the small, theme-aware feature "spot" illustrations in
 * resources/views/components/spots, plus keyword matching that picks a spot
 * (or a matching icon) for a feature, benefit or challenge from its text.
 */
final class SpotRegistry
{
    /**
     * Keyword groups in priority order: the first group with a match wins.
     * Patterns are matched case-insensitively from the start of a word.
     *
     * @var array<string, array{pattern: string, icon: string}>
     */
    private const GROUPS = [
        'dashboard' => ['pattern' => 'report|dashboard|portal|analytic|kpi|insight', 'icon' => 'presentation-chart-line'],
        'deploy' => ['pattern' => 'deploy|ci\b|cd\b|devops|release|(?<!data )pipelines?\b|test(?!imon)', 'icon' => 'rocket-launch'],
        'workflow' => ['pattern' => 'workflow|approval|automat|process|orchestrat', 'icon' => 'arrow-path-rounded-square'],
        'integration' => ['pattern' => 'integrat|api\b|apis\b|third-party|connect|sync|webhook', 'icon' => 'arrows-right-left'],
        'security' => ['pattern' => 'secur|access(?!ib)|audit|encrypt|complian|privacy|role|permission|authenticat|threat|vulnerab', 'icon' => 'shield-check'],
        'mobile' => ['pattern' => 'mobile|app store|offline|ios\b|android|cross-platform|push notification', 'icon' => 'device-phone-mobile'],
        'cloud' => ['pattern' => 'cloud|migrat|hosting|scal|serverless|infrastructure', 'icon' => 'cloud'],
        'ai' => ['pattern' => 'ai\b|model|predict|llm|agent|assistant|machine learning|nlp\b|vision|generative|intelligen', 'icon' => 'cpu-chip'],
        'document' => ['pattern' => 'document|search|retriev|knowledge|content|citation', 'icon' => 'magnifying-glass-circle'],
        'data' => ['pattern' => 'data|warehouse|etl\b|database|bi\b', 'icon' => 'circle-stack'],
        'commerce' => ['pattern' => 'payment|checkout|commerce|cart\b|carts\b|(?<!in )orders?\b|subscription|billing|invoice|storefront', 'icon' => 'shopping-cart'],
        'monitoring' => ['pattern' => 'monitor|performance|uptime|alert|observab|optimi|logging', 'icon' => 'arrow-trending-up'],
        'design' => ['pattern' => 'design|ui\b|ux\b|prototyp|brand|accessib|usability|interface', 'icon' => 'paint-brush'],
        'support' => ['pattern' => 'support|maintenance|ticket|sla\b|help|incident', 'icon' => 'lifebuoy'],
        'chat' => ['pattern' => 'notification|email|chat|messag|communicat|conversation', 'icon' => 'chat-bubble-left-right'],
    ];

    /**
     * Spots used when no keyword matches (picked deterministically from a seed).
     *
     * @var array<int, string>
     */
    private const GENERIC = ['spark', 'workflow', 'dashboard', 'integration'];

    /**
     * Closest alternatives, used so one grid never shows the same spot twice.
     *
     * @var array<string, array<int, string>>
     */
    private const RELATED = [
        'dashboard' => ['data', 'monitoring'], 'deploy' => ['monitoring', 'cloud'], 'workflow' => ['document', 'integration'],
        'integration' => ['data', 'cloud'], 'security' => ['monitoring', 'document'], 'mobile' => ['chat', 'design'],
        'cloud' => ['deploy', 'data'], 'ai' => ['chat', 'document'], 'document' => ['data', 'workflow'],
        'data' => ['dashboard', 'cloud'], 'commerce' => ['dashboard', 'integration'], 'monitoring' => ['dashboard', 'deploy'],
        'design' => ['mobile', 'spark'], 'support' => ['chat', 'monitoring'], 'chat' => ['support', 'mobile'],
    ];

    /**
     * @return array<string, string> name => label
     */
    public static function all(): array
    {
        return [
            'dashboard' => 'Dashboards & reporting',
            'workflow' => 'Workflows & approvals',
            'integration' => 'Integrations & APIs',
            'security' => 'Security & access control',
            'mobile' => 'Mobile apps',
            'cloud' => 'Cloud & scalability',
            'ai' => 'AI & machine learning',
            'document' => 'Documents, search & knowledge',
            'data' => 'Data pipelines & warehousing',
            'commerce' => 'Payments & commerce',
            'monitoring' => 'Monitoring & performance',
            'design' => 'Design & user experience',
            'support' => 'Support & maintenance',
            'chat' => 'Messaging & notifications',
            'deploy' => 'CI/CD & releases',
            'spark' => 'General (abstract)',
        ];
    }

    public static function isValid(?string $name): bool
    {
        return $name !== null && array_key_exists($name, self::all());
    }

    /**
     * Best spot for a piece of text (e.g. "Role-based portals and dashboards" → dashboard).
     */
    public static function forText(string $text, ?string $fallbackSeed = null): string
    {
        return self::match($text) ?? self::GENERIC[crc32($fallbackSeed ?? $text) % count(self::GENERIC)];
    }

    /**
     * Spots for a list of items without repeats: a spot already used falls back to a related one,
     * then to any unused spot, before repeating.
     *
     * @param  array<int, string>  $texts
     * @return array<int, string>
     */
    public static function distinct(array $texts): array
    {
        $used = [];
        $result = [];

        foreach ($texts as $index => $text) {
            $first = self::forText($text);
            $candidates = [$first, ...(self::RELATED[$first] ?? []), ...self::GENERIC, ...array_keys(self::all())];
            $spot = collect($candidates)->first(fn (string $name) => ! in_array($name, $used, true)) ?? $first;

            $used[] = $spot;
            $result[$index] = $spot;
        }

        return $result;
    }

    /**
     * Matching icon from the IconRegistry whitelist, or the given fallback.
     */
    public static function iconFor(string $text, string $fallback = 'sparkles'): string
    {
        $group = self::match($text);

        if ($group !== null) {
            return self::GROUPS[$group]['icon'];
        }

        return IconRegistry::isValid($fallback) ? $fallback : 'sparkles';
    }

    private static function match(string $text): ?string
    {
        foreach (self::GROUPS as $name => $group) {
            if (preg_match('~\b(?:'.$group['pattern'].')~iu', $text) === 1) {
                return $name;
            }
        }

        return null;
    }
}
