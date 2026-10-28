<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Enums\SectionType;
use App\Models\BlogPost;
use App\Models\Industry;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Service;
use Database\Seeders\ContentRefreshSeeder;
use Database\Seeders\CoreContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentSeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(CoreContentSeeder::class);
    }

    public function test_insights_are_published_genuine_articles_with_seo_metadata(): void
    {
        $posts = BlogPost::query()->where('is_demo', false)->where('status', PostStatus::Published)->with(['seo', 'tags'])->get();

        $this->assertCount(14, $posts);
        $this->assertSame(1, $posts->where('is_featured', true)->count());
        $this->assertContains('how-to-identify-high-value-ai-use-cases-in-your-business', $posts->pluck('slug'));

        foreach ($posts as $post) {
            $this->assertGreaterThanOrEqual(900, $this->wordCount($post->body), $post->slug);
            $this->assertNotNull($post->blog_category_id, $post->slug);
            $this->assertNotEmpty($post->tags, $post->slug);
            $this->assertNotNull($post->seo, $post->slug);
            $this->assertNotEmpty($post->seo->meta_title, $post->slug);
            $this->assertLessThanOrEqual(60, mb_strlen($post->seo->meta_title), $post->slug);
            $this->assertNotEmpty($post->seo->meta_description, $post->slug);
            $this->assertLessThanOrEqual(160, mb_strlen($post->seo->meta_description), $post->slug);
        }
    }

    public function test_every_service_has_a_long_description_and_faqs(): void
    {
        $services = Service::query()->with('faqs')->get();

        $this->assertCount(18, $services);

        foreach ($services as $service) {
            $this->assertGreaterThanOrEqual(300, $this->wordCount($service->description), $service->slug);
            $this->assertGreaterThanOrEqual(3, $service->faqs->count(), $service->slug);
            $this->assertSame('services', $service->faqs->first()->category->slug);
        }

        $this->get('/services/custom-software-development')
            ->assertOk()
            ->assertSee('How long does a custom software project take?');
    }

    public function test_every_industry_has_a_description_and_seo_metadata(): void
    {
        foreach (Industry::query()->with('seo')->get() as $industry) {
            $this->assertGreaterThanOrEqual(180, $this->wordCount($industry->description), $industry->slug);
            $this->assertLessThanOrEqual(60, mb_strlen((string) $industry->seo?->meta_title), $industry->slug);
            $this->assertNotEmpty($industry->seo?->meta_description, $industry->slug);
        }
    }

    public function test_internal_links_in_seeded_content_resolve(): void
    {
        $html = BlogPost::query()->pluck('body')
            ->merge(Service::query()->pluck('description'))
            ->merge(Industry::query()->pluck('description'))
            ->merge(Page::query()->where('slug', 'home')->firstOrFail()->sections()->pluck('content'))
            ->implode(' ');

        preg_match_all('/href="(\/[^"#?]*)/', $html, $matches);
        $paths = array_values(array_unique(array_filter($matches[1], fn (string $path): bool => ! str_starts_with($path, '//'))));

        $this->assertNotEmpty($paths);

        foreach ($paths as $path) {
            $this->assertSame(200, $this->get($path)->getStatusCode(), "Broken internal link: {$path}");
        }
    }

    public function test_home_page_has_seo_metadata_and_an_introduction_before_the_faq(): void
    {
        $home = Page::query()->where('slug', 'home')->with('seo')->firstOrFail();

        $this->assertSame('Softec Ai Solution | Custom Software, AI & Cloud Development', $home->seo?->meta_title);
        $this->assertBetween(120, 155, mb_strlen((string) $home->seo->meta_description));
        $this->assertIntroductionPrecedesFaq($home);
        $this->assertStatementFollowsServices($home);

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>'.e($home->seo->meta_title).'</title>', false)
            ->assertSee('<meta name="description" content="'.e($home->seo->meta_description).'">', false)
            ->assertSee('id="'.CoreContentSeeder::HOME_INTRO_ANCHOR.'"', false)
            ->assertSee('About Softec Ai Solution')
            ->assertSee('id="'.CoreContentSeeder::HOME_STATEMENT_ANCHOR.'"', false)
            ->assertSee('data-scrub-words', false);

        foreach (['about', 'engagement-models'] as $slug) {
            $this->assertNotEmpty(Page::query()->where('slug', $slug)->firstOrFail()->seo?->meta_title, $slug);
        }
    }

    public function test_content_refresh_seeder_updates_an_existing_database_idempotently(): void
    {
        // Simulate a database seeded before this content existed.
        $home = Page::query()->where('slug', 'home')->firstOrFail();
        $home->sections()->whereIn('anchor', [CoreContentSeeder::HOME_INTRO_ANCHOR, CoreContentSeeder::HOME_STATEMENT_ANCHOR])->delete();
        $post = BlogPost::query()->where('slug', 'how-to-identify-high-value-ai-use-cases-in-your-business')->firstOrFail();
        $post->update(['is_demo' => true, 'body' => '<p>Short demo article.</p>']);
        $service = Service::query()->where('slug', 'custom-software-development')->firstOrFail();
        $service->update(['description' => '<p>Short description.</p>']);

        $this->seed(ContentRefreshSeeder::class);
        $counts = $this->contentCounts();

        $this->seed(ContentRefreshSeeder::class);
        $this->seed(CoreContentSeeder::class);

        $this->assertSame($counts, $this->contentCounts());
        $this->assertFalse($post->fresh()->is_demo);
        $this->assertGreaterThanOrEqual(900, $this->wordCount($post->fresh()->body));
        $this->assertGreaterThanOrEqual(300, $this->wordCount($service->fresh()->description));
        $this->assertSame('Custom Software Development Company | Softec Ai Solution', $service->seo()->first()?->meta_title);
        $this->assertSame(1, $home->sections()->where('anchor', CoreContentSeeder::HOME_INTRO_ANCHOR)->count());
        $this->assertIntroductionPrecedesFaq($home);
        $this->assertStatementFollowsServices($home);
    }

    private function assertStatementFollowsServices(Page $page): void
    {
        $sections = $page->sections()->orderBy('sort_order')->get()->values();
        $statement = $sections->search(fn (PageSection $section): bool => $section->anchor === CoreContentSeeder::HOME_STATEMENT_ANCHOR);

        $this->assertNotFalse($statement);
        $this->assertSame(SectionType::Statement, $sections[$statement]->type);
        $this->assertSame(SectionType::Services, $sections[$statement - 1]->type);
    }

    private function assertIntroductionPrecedesFaq(Page $page): void
    {
        $sections = $page->sections()->orderBy('sort_order')->get()->values();
        $intro = $sections->search(fn (PageSection $section): bool => $section->anchor === CoreContentSeeder::HOME_INTRO_ANCHOR);

        $this->assertNotFalse($intro);
        $this->assertSame(SectionType::RichText, $sections[$intro]->type);
        $this->assertSame(SectionType::Faq, $sections[$intro + 1]->type);
    }

    private function assertBetween(int $min, int $max, int $actual): void
    {
        $this->assertGreaterThanOrEqual($min, $actual);
        $this->assertLessThanOrEqual($max, $actual);
    }

    /**
     * @return array<string, int>
     */
    private function contentCounts(): array
    {
        $tables = ['blog_posts', 'blog_tags', 'blog_post_tag', 'faqs', 'faq_categories', 'faqables', 'seo_meta', 'page_sections'];

        return collect($tables)->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()])->all();
    }

    private function wordCount(?string $html): int
    {
        return str_word_count(strip_tags((string) $html));
    }
}
