<?php

namespace Database\Seeders\Concerns;

use App\Enums\PostStatus;
use App\Enums\SectionBackground;
use App\Enums\SectionType;
use App\Models\Author;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Industry;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Long-form editorial content shared by CoreContentSeeder (fresh installs) and
 * ContentRefreshSeeder (existing databases): service and industry copy, SEO
 * metadata, service FAQs, the home page introduction and Insights articles.
 *
 * Without $overwrite only missing content is added, so changes made in the
 * admin panel survive re-seeding. With $overwrite the seeded copy replaces
 * the stored copy. Both modes are idempotent.
 */
trait SeedsRichContent
{
    public const HOME_INTRO_ANCHOR = 'about-softec-ai-solution';

    public const HOME_STATEMENT_ANCHOR = 'our-approach';

    /**
     * Scroll statement shown after the services on the home page.
     *
     * @var array{eyebrow: string, title: string, subtitle: string}
     */
    public const HOME_STATEMENT = [
        'eyebrow' => 'Our approach',
        'title' => 'We combine product thinking, modern engineering and practical AI to build software people enjoy using and businesses can rely on for years.',
        'subtitle' => 'Clear plans, honest progress reports and well-documented code, with the custom code we build for you owned by you as agreed in your contract.',
    ];

    protected function seedRichContent(bool $overwrite): void
    {
        $this->seedServiceContent($overwrite);
        $this->seedIndustryContent($overwrite);
        $this->seedPageContent($overwrite);
        $this->seedInsights($overwrite);
    }

    /**
     * @return array<string, array{seo: array<string, string>, faqs: array<int, array{0: string, 1: string}>, description: string}>
     */
    protected function serviceContent(): array
    {
        return require dirname(__DIR__).'/data/service-content.php';
    }

    /**
     * @return array<string, array{seo: array<string, string>, description: string}>
     */
    protected function industryContent(): array
    {
        return require dirname(__DIR__).'/data/industry-content.php';
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function pageContent(): array
    {
        return require dirname(__DIR__).'/data/page-seo.php';
    }

    protected function editorialAuthor(): Author
    {
        return Author::query()->firstOrCreate(['slug' => 'softec-ai-editorial-team'], [
            'name' => 'Softec Ai Editorial Team',
            'job_title' => 'Engineering & product insights',
            'bio' => 'Articles written and reviewed by the engineers, designers and consultants at Softec Ai Solution.',
            'is_active' => true,
        ]);
    }

    private function seedServiceContent(bool $overwrite): void
    {
        $category = FaqCategory::query()->firstOrCreate(['slug' => 'services'], [
            'name' => 'Services',
            'sort_order' => (int) FaqCategory::query()->max('sort_order') + 1,
            'is_active' => true,
        ]);

        $faqOrder = 0;

        foreach ($this->serviceContent() as $slug => $content) {
            $service = Service::query()->where('slug', $slug)->first();

            if ($service === null) {
                $faqOrder += count($content['faqs']);

                continue;
            }

            if ($overwrite) {
                $service->update(['description' => $content['description']]);
            }

            $this->applySeo($service, $content['seo'], $overwrite);

            if (! $overwrite && $service->faqs()->exists()) {
                $faqOrder += count($content['faqs']);

                continue;
            }

            $pivot = [];

            foreach ($content['faqs'] as $position => [$question, $answer]) {
                $faq = Faq::query()->firstOrNew(['faq_category_id' => $category->getKey(), 'question' => $question]);

                if (! $faq->exists || $overwrite) {
                    $faq->fill(['answer' => '<p>'.e($answer).'</p>', 'show_on_home' => false, 'sort_order' => $faqOrder, 'is_active' => true])->save();
                }

                $faqOrder++;
                $pivot[$faq->getKey()] = ['sort_order' => $position];
            }

            $service->faqs()->syncWithoutDetaching($pivot);
        }
    }

    private function seedIndustryContent(bool $overwrite): void
    {
        foreach ($this->industryContent() as $slug => $content) {
            $industry = Industry::query()->where('slug', $slug)->first();

            if ($industry === null) {
                continue;
            }

            if ($overwrite) {
                $industry->update(['description' => $content['description']]);
            }

            $this->applySeo($industry, $content['seo'], $overwrite);
        }
    }

    private function seedPageContent(bool $overwrite): void
    {
        $content = $this->pageContent();

        foreach (['home', 'about', 'engagement-models'] as $slug) {
            $page = Page::query()->where('slug', $slug)->first();

            if ($page !== null) {
                $this->applySeo($page, $content[$slug], $overwrite);
            }
        }

        $home = Page::query()->where('slug', 'home')->first();

        // Fresh installs receive the introduction with the other home sections;
        // only an explicit refresh adds it to (or updates it on) an existing page.
        if ($home !== null && $overwrite) {
            $this->refreshHomeIntro($home, $content['home_intro']);
            $this->refreshHomeStatement($home);
        }
    }

    /**
     * Adds the scroll statement after the services section of an existing home page.
     */
    private function refreshHomeStatement(Page $home): void
    {
        if ($home->sections()->where('anchor', self::HOME_STATEMENT_ANCHOR)->exists()) {
            return;
        }

        $services = $home->sections()->where('type', SectionType::Services)->orderBy('sort_order')->first();
        $position = $services !== null ? $services->sort_order + 1 : (int) $home->sections()->max('sort_order') + 1;

        $home->sections()->where('sort_order', '>=', $position)->increment('sort_order');

        $home->sections()->create(self::HOME_STATEMENT + [
            'type' => SectionType::Statement,
            'anchor' => self::HOME_STATEMENT_ANCHOR,
            'sort_order' => $position,
            'is_enabled' => true,
        ]);
    }

    /**
     * @param  array<string, string>  $intro
     */
    private function refreshHomeIntro(Page $home, array $intro): void
    {
        $attributes = Arr::only($intro, ['eyebrow', 'title', 'content']);
        $section = $home->sections()->where('anchor', self::HOME_INTRO_ANCHOR)->first();

        if ($section !== null) {
            $section->update($attributes);

            return;
        }

        $faq = $home->sections()->where('type', SectionType::Faq)->orderBy('sort_order')->first();
        $position = $faq?->sort_order ?? (int) $home->sections()->max('sort_order') + 1;

        $home->sections()->where('sort_order', '>=', $position)->increment('sort_order');

        $home->sections()->create($attributes + [
            'type' => SectionType::RichText,
            'anchor' => self::HOME_INTRO_ANCHOR,
            'background' => SectionBackground::Muted,
            'sort_order' => $position,
            'is_enabled' => true,
        ]);
    }

    private function seedInsights(bool $overwrite): void
    {
        $insights = require dirname(__DIR__).'/data/insights.php';

        foreach ($insights['tags'] as $slug => $name) {
            BlogTag::query()->firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $author = $this->editorialAuthor();
        $categories = BlogCategory::query()->pluck('id', 'slug');
        $tags = BlogTag::query()->pluck('id', 'slug');

        foreach ($insights['posts'] as $data) {
            $post = BlogPost::withTrashed()->where('slug', $data['slug'])->first();

            // Respect posts an editor has deleted, and leave existing posts alone unless refreshing.
            if ($post?->trashed() || ($post !== null && ! $overwrite)) {
                continue;
            }

            $attributes = [
                'author_id' => $author->getKey(),
                'blog_category_id' => $categories[$data['category']] ?? null,
                'title' => $data['title'],
                'excerpt' => $data['excerpt'],
                'body' => $data['body'],
                'is_featured' => $data['is_featured'],
                'is_demo' => false,
            ];

            if ($post === null) {
                $post = BlogPost::query()->create($attributes + [
                    'slug' => $data['slug'],
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays($data['days_ago']),
                    'allow_comments' => true,
                ]);
            } else {
                // Former demonstration posts become genuine articles on the editorial schedule.
                if ($post->is_demo) {
                    $attributes += ['status' => PostStatus::Published, 'published_at' => now()->subDays($data['days_ago'])];
                }

                $post->update($attributes);
            }

            $post->tags()->syncWithoutDetaching($tags->only($data['tags'])->values()->all());
            $this->applySeo($post, $data['seo'], $overwrite);
        }
    }

    /**
     * Creates the record's SEO metadata, or updates it: every field when
     * overwriting, otherwise only the fields that are still empty.
     *
     * @param  array<string, string>  $seo
     */
    private function applySeo(Model $model, array $seo, bool $overwrite): void
    {
        $attributes = Arr::only($seo, ['meta_title', 'meta_description', 'focus_keyword', 'keywords']);
        $meta = $model->seo()->first();

        if ($meta === null) {
            $model->seo()->create($attributes);

            return;
        }

        $meta->update($overwrite ? $attributes : array_filter(
            $attributes,
            fn (string $value, string $key): bool => blank($meta->getAttribute($key)),
            ARRAY_FILTER_USE_BOTH,
        ));
    }
}
