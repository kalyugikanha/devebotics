<?php

namespace Tests\Feature\Public;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\CoreContentSeeder;
use Database\Seeders\DemoContentSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed([PermissionSeeder::class, CoreContentSeeder::class, DemoContentSeeder::class]);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function publicUrls(): array
    {
        return [
            'home' => ['/'],
            'about' => ['/about'],
            'services' => ['/services'],
            'service' => ['/services/custom-software-development'],
            'products' => ['/products'],
            'product' => ['/products/orbit-crm'],
            'products filtered' => ['/products?category=crm-erp-platforms'],
            'industries' => ['/industries'],
            'industry' => ['/industries/healthcare'],
            'case studies' => ['/case-studies'],
            'case study' => ['/case-studies/patient-engagement-platform'],
            'blog' => ['/blog'],
            'blog post' => ['/blog/how-to-identify-high-value-ai-use-cases-in-your-business'],
            'blog category' => ['/blog/category/artificial-intelligence'],
            'blog tag' => ['/blog/tag/generative-ai'],
            'blog author' => ['/blog/author/softec-ai-editorial-team'],
            'testimonials' => ['/testimonials'],
            'feedback' => ['/feedback'],
            'contact' => ['/contact'],
            'project' => ['/start-a-project'],
            'careers' => ['/careers'],
            'job' => ['/careers/senior-laravel-developer'],
            'open application' => ['/careers/apply'],
            'team' => ['/team'],
            'faq' => ['/faq'],
            'search' => ['/search?q=cloud'],
            'cms page' => ['/engagement-models'],
            'legal page' => ['/privacy-policy'],
        ];
    }

    #[DataProvider('publicUrls')]
    public function test_public_pages_render(string $url): void
    {
        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee('<main id="main"', false);
        $response->assertSee('Skip to main content');

        // Guard against uncompiled Blade directives leaking into the markup.
        foreach (['@endif', '@if (', '@foreach', '@endforeach', '{{ $'] as $raw) {
            $response->assertDontSee($raw, false);
        }
    }

    public function test_homepage_shows_dynamic_sections_and_demo_labels(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Custom Software Development')
            ->assertSee('class="badge-demo"', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_draft_and_scheduled_content_is_not_public(): void
    {
        $draft = Service::factory()->draft()->create(['slug' => 'secret-draft']);
        $scheduled = Service::factory()->scheduled()->create(['slug' => 'future-service']);
        BlogPost::factory()->scheduled()->create(['slug' => 'future-post']);

        $this->get('/services/secret-draft')->assertNotFound();
        $this->get('/services/future-service')->assertNotFound();
        $this->get('/blog/future-post')->assertNotFound();
        $this->get('/services')->assertDontSee($draft->title)->assertDontSee($scheduled->title);
    }

    public function test_unpublished_page_returns_404_and_unknown_urls_render_branded_404(): void
    {
        Page::factory()->draft()->create(['slug' => 'hidden-page']);

        $this->get('/hidden-page')->assertNotFound();
        $this->get('/a/b/c')->assertNotFound()->assertSee('We can’t find that page');
    }

    public function test_preview_requires_signature_authentication_and_permission(): void
    {
        $product = Product::factory()->draft()->create(['name' => 'Stealth Product']);
        $url = URL::temporarySignedRoute('preview.show', now()->addHour(), ['type' => 'product', 'id' => $product->id]);

        $this->get($url)->assertRedirect(route('filament.admin.auth.login'));

        $this->actingAs(User::factory()->withRole('Support Agent')->create())->get($url)->assertForbidden();

        $this->actingAs(User::factory()->withRole('Product Manager')->create())
            ->get($url)
            ->assertOk()
            ->assertSee('Stealth Product')
            ->assertSee('noindex', false);

        $this->get(route('preview.show', ['type' => 'product', 'id' => $product->id]))->assertForbidden();
    }

    public function test_disabled_features_return_404(): void
    {
        settings()->setMany('features', ['careers_enabled' => false, 'feedback_form_enabled' => false]);

        $this->get('/careers')->assertNotFound();
        $this->get('/feedback')->assertNotFound();
    }

    public function test_navigation_hides_links_to_unpublished_content(): void
    {
        $service = Service::query()->where('is_featured', true)->firstOrFail();
        $service->update(['status' => 'draft']);

        $this->get('/')->assertDontSee('href="'.url('/services/'.$service->slug).'"', false);
    }
}
