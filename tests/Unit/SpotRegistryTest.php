<?php

namespace Tests\Unit;

use App\Support\Art\SpotRegistry;
use App\Support\IconRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SpotRegistryTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function featureTitles(): array
    {
        return [
            'portals and dashboards' => ['Role-based portals and dashboards', 'dashboard'],
            'third-party integrations' => ['Third-party integrations', 'integration'],
            'workflow engines' => ['Workflow and approval engines', 'workflow'],
            'reporting' => ['Reporting and exports', 'dashboard'],
            'audited access' => ['Secure, audited access', 'security'],
            'offline apps' => ['Offline-first apps for iOS and Android', 'mobile'],
            'cloud migration' => ['Zero-downtime cloud migration', 'cloud'],
            'llm assistants' => ['LLM-powered assistants grounded in your data', 'ai'],
            'knowledge search' => ['Search across your knowledge base', 'document'],
            'warehouse' => ['A single data warehouse for every team', 'data'],
            'checkout' => ['Fast checkout and subscription billing', 'commerce'],
            'uptime' => ['Uptime monitoring and alerting', 'monitoring'],
            'accessible ui' => ['Accessible, consistent UI components', 'design'],
            'sla support' => ['Maintenance with agreed SLAs', 'support'],
            'notifications' => ['Email and in-app notifications', 'chat'],
            'ci/cd' => ['CI/CD pipelines with automated testing', 'deploy'],
        ];
    }

    #[DataProvider('featureTitles')]
    public function test_feature_text_maps_to_a_relevant_spot(string $text, string $expected): void
    {
        $this->assertSame($expected, SpotRegistry::forText($text));
    }

    public function test_keywords_match_whole_word_starts_only(): void
    {
        $this->assertNotSame('ai', SpotRegistry::forText('Detailed email campaigns'));
        $this->assertNotSame('deploy', SpotRegistry::forText('Client testimonials'));
        $this->assertNotSame('commerce', SpotRegistry::forText('Planning in order to grow'));
        $this->assertSame('design', SpotRegistry::forText('Accessibility reviews'));
    }

    public function test_unmatched_text_falls_back_deterministically_to_a_registered_spot(): void
    {
        $first = SpotRegistry::forText('Something entirely different', 'seed-1');

        $this->assertTrue(SpotRegistry::isValid($first));
        $this->assertSame($first, SpotRegistry::forText('Something entirely different', 'seed-1'));
    }

    public function test_every_registered_spot_has_a_view(): void
    {
        foreach (array_keys(SpotRegistry::all()) as $name) {
            $this->assertFileExists(resource_path("views/components/spots/{$name}.blade.php"), $name);
        }

        $this->assertCount(16, SpotRegistry::all());
        $this->assertFalse(SpotRegistry::isValid('../../layouts/app'));
        $this->assertFalse(SpotRegistry::isValid(null));
    }

    public function test_icon_for_returns_whitelisted_icons(): void
    {
        foreach (self::featureTitles() as [$text]) {
            $this->assertTrue(IconRegistry::isValid(SpotRegistry::iconFor($text)), $text);
        }

        $this->assertSame('code-bracket', SpotRegistry::iconFor('Something entirely different', 'code-bracket'));
        $this->assertSame('sparkles', SpotRegistry::iconFor('Something entirely different', 'not-an-icon'));
    }

    public function test_distinct_never_repeats_a_spot_within_one_grid(): void
    {
        $spots = SpotRegistry::distinct([
            'Business process digitisation',
            'Role-based portals and dashboards',
            'Workflow and approval engines',
            'Reporting and exports',
            'Third-party integrations',
            'Secure, audited access',
        ]);

        $this->assertSame('workflow', $spots[0]);
        $this->assertSame('dashboard', $spots[1]);
        $this->assertSame('document', $spots[2], 'A second workflow item falls back to a related spot.');
        $this->assertSame('data', $spots[3], 'A second dashboard item falls back to a related spot.');
        $this->assertCount(6, array_unique($spots));
    }
}
