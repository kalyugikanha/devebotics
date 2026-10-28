<?php

namespace Tests\Feature\Public;

use App\Models\JobOpening;
use App\Models\Service;
use App\Models\Testimonial;
use Database\Seeders\CoreContentSeeder;
use Database\Seeders\DemoContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed([CoreContentSeeder::class, DemoContentSeeder::class]);
    }

    private function enableIndexing(): void
    {
        $this->app['env'] = 'production';
        config(['softec.allow_indexing' => true]);
    }

    public function test_service_page_has_title_description_canonical_and_og_tags(): void
    {
        $service = Service::query()->where('slug', 'custom-software-development')->firstOrFail();

        $this->get('/services/custom-software-development')
            ->assertSee('<title>Custom Software Development Company | Softec Ai Solution</title>', false)
            ->assertSee('<meta name="description" content="'.e($service->seo->meta_description).'">', false)
            ->assertSee('<link rel="canonical" href="'.url('/services/custom-software-development').'">', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('"@type":"Service"', false)
            ->assertSee('"@type":"BreadcrumbList"', false);
    }

    public function test_admin_seo_overrides_are_used(): void
    {
        $service = Service::query()->where('slug', 'custom-software-development')->firstOrFail();
        $service->seo()->updateOrCreate([], ['meta_title' => 'Bespoke Software Engineering', 'meta_description' => 'Custom override description.', 'robots_index' => false, 'robots_follow' => true]);

        $this->enableIndexing();

        $this->get('/services/custom-software-development')
            ->assertSee('<title>Bespoke Software Engineering | Softec Ai Solution</title>', false)
            ->assertSee('Custom override description.')
            ->assertSee('content="noindex,follow"', false);
    }

    public function test_non_production_environments_are_always_noindex(): void
    {
        config(['softec.allow_indexing' => true]);

        $this->get('/')->assertSee('<meta name="robots" content="noindex,follow">', false);
        $this->get('/robots.txt')->assertOk()->assertSee("Disallow: /\n", false);
        $this->get('/sitemaps/services.xml')->assertOk()->assertDontSee('<loc>', false);
    }

    public function test_production_robots_and_sitemaps_list_public_urls(): void
    {
        $this->enableIndexing();
        Service::factory()->draft()->create(['slug' => 'draft-only-service']);

        $this->get('/')->assertSee('content="index,follow,max-image-preview:large"', false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: '.route('sitemap'));

        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->assertSee(route('sitemap.type', 'services'));

        $this->get('/sitemaps/services.xml')
            ->assertOk()
            ->assertSee(url('/services/custom-software-development'))
            ->assertDontSee('draft-only-service');
    }

    public function test_search_and_filtered_listing_pages_are_noindex(): void
    {
        $this->enableIndexing();

        $this->get('/search?q=cloud')->assertSee('content="noindex', false);
        $this->get('/products?category=saas-products')->assertSee('content="noindex', false)->assertSee('<link rel="canonical" href="'.route('products.index').'">', false);
    }

    public function test_homepage_outputs_organization_and_website_schema(): void
    {
        $this->get('/')->assertSee('"@type":"Organization"', false)->assertSee('"@type":"WebSite"', false);
    }

    public function test_blog_post_has_article_schema(): void
    {
        $this->get('/blog/how-to-identify-high-value-ai-use-cases-in-your-business')
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('property="og:type" content="article"', false);
    }

    public function test_job_posting_schema_only_for_real_open_vacancies(): void
    {
        $this->get('/careers/senior-laravel-developer')->assertDontSee('"@type":"JobPosting"', false);

        JobOpening::factory()->create(['slug' => 'real-role', 'is_demo' => false]);
        $this->get('/careers/real-role')->assertSee('"@type":"JobPosting"', false);

        JobOpening::factory()->closed()->create(['slug' => 'closed-role', 'is_demo' => false]);
        $this->get('/careers/closed-role')->assertDontSee('"@type":"JobPosting"', false)->assertSee('no longer accepting applications');
    }

    public function test_testimonials_page_never_outputs_self_serving_review_markup(): void
    {
        Testimonial::factory()->approved()->create();

        $this->get('/testimonials')->assertOk()->assertDontSee('AggregateRating')->assertDontSee('"@type":"Review"', false);
    }
}
