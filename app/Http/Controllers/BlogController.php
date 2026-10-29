<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Service;
use App\Support\Html\ContentProcessor;
use App\Support\Seo\SchemaFactory;
use App\Support\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $search = mb_substr(trim((string) $request->string('q')), 0, 80);

        $featured = $search === '' && ! $request->has('page')
            ? BlogPost::query()->published()->where('is_featured', true)->with(['author', 'category', 'featuredMedia'])->latest('published_at')->first()
            : null;

        $posts = $this->baseQuery()
            ->when($featured, fn (Builder $q) => $q->whereKeyNot($featured->getKey()))
            ->when($search !== '', fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('title', 'like', '%'.addcslashes($search, '%_\\').'%')
                ->orWhere('excerpt', 'like', '%'.addcslashes($search, '%_\\').'%')))
            ->paginate(9)
            ->withQueryString();

        $seo = SeoData::make('Insights & Blog', 'Articles from Softec Ai Solution on artificial intelligence, software engineering, cloud, product design and digital transformation.')
            ->canonical(route('blog.index'))
            ->breadcrumbs([['Insights', null]]);

        if ($search !== '') {
            $seo->noindex();
        }

        return view('blog.index', [
            'posts' => $posts,
            'featured' => $featured,
            'search' => $search,
            'heading' => 'Insights & ideas',
            'intro' => 'Practical perspectives from our engineers, designers and consultants.',
            'categories' => $this->categories(),
            'seo' => $seo,
        ]);
    }

    public function category(string $slug): View
    {
        $category = BlogCategory::query()->active()->where('slug', $slug)->firstOrFail();

        return $this->archive($this->baseQuery()->where('blog_category_id', $category->getKey()), $category->name, $category->description, $category->publicUrl(), $category->getKey());
    }

    public function tag(string $slug): View
    {
        $tag = BlogTag::query()->where('slug', $slug)->firstOrFail();

        return $this->archive($this->baseQuery()->whereHas('tags', fn (Builder $q) => $q->whereKey($tag->getKey())), '#'.$tag->name, 'Articles tagged '.$tag->name.'.', $tag->publicUrl());
    }

    public function author(string $slug): View
    {
        $author = Author::query()->active()->where('slug', $slug)->with('avatar')->firstOrFail();

        return $this->archive($this->baseQuery()->where('author_id', $author->getKey()), $author->name, $author->bio, $author->publicUrl(), author: $author);
    }

    public function show(string $slug): View
    {
        return $this->render(BlogPost::query()->published()->where('slug', $slug)->firstOrFail());
    }

    public function render(BlogPost $post, bool $isPreview = false): View
    {
        $post->load(['author.avatar', 'category', 'tags', 'featuredMedia', 'seo.ogImage']);

        $commentsEnabled = (bool) settings('features.blog_comments_enabled') && $post->allow_comments;
        $comments = $commentsEnabled
            ? $post->comments()->approved()->whereNull('parent_id')->with(['replies' => fn ($q) => $q->approved()->oldest()])->oldest()->get()
            : collect();

        $tagIds = $post->tags->modelKeys();
        $related = BlogPost::query()->published()->whereKeyNot($post->getKey())
            ->with(['author', 'category', 'featuredMedia'])
            ->where(fn (Builder $q) => $q->where('blog_category_id', $post->blog_category_id)
                ->when($tagIds !== [], fn (Builder $w) => $w->orWhereHas('tags', fn (Builder $t) => $t->whereIn('blog_tags.id', $tagIds))))
            ->latest('published_at')->limit(3)->get();

        $content = app(ContentProcessor::class)->process($post->body);

        $seo = SeoData::forModel($post, $post->title, $post->excerpt, $post->featuredMedia)
            ->type('article')
            ->canonical($post->publicUrl())
            ->breadcrumbs(array_values(array_filter([
                ['Insights', route('blog.index')],
                $post->category ? [$post->category->name, $post->category->publicUrl()] : null,
                [$post->title, null],
            ])));

        $seo->publishedTime = $post->published_at?->toIso8601String();
        $seo->withSchema(SchemaFactory::article($post, $seo->image));

        if ($isPreview) {
            $seo->noindex();
        }

        $postServiceMap = [
            'what-are-ai-agents-a-practical-guide-for-business-leaders' => 'generative-ai-ai-agent-development',
            'retrieval-augmented-generation-explained-for-business-leaders' => 'generative-ai-ai-agent-development',
            'generative-ai-use-cases-for-small-and-mid-sized-businesses' => 'generative-ai-ai-agent-development',
            'how-to-identify-high-value-ai-use-cases-in-your-business' => 'ai-machine-learning-solutions',
            'how-much-does-custom-software-development-cost' => 'custom-software-development',
            'how-to-choose-a-software-development-company' => 'custom-software-development',
            'custom-software-vs-off-the-shelf-how-to-choose' => 'custom-software-development',
            'web-app-vs-mobile-app-vs-pwa' => 'web-application-development',
            'saas-product-development-from-mvp-to-scale' => 'saas-product-development',
            'business-process-automation-where-to-start' => 'business-process-automation',
            'cybersecurity-checklist-for-growing-businesses' => 'cybersecurity-consulting',
            'a-practical-checklist-for-migrating-to-the-cloud' => 'cloud-computing-cloud-migration',
            'laravel-for-business-web-applications' => 'web-application-development',
            'why-design-systems-speed-up-product-development' => 'uiux-design',
        ];

        $serviceSlug = $postServiceMap[$post->slug] ?? 'custom-software-development';
        $featuredService = Service::query()->published()->where('slug', $serviceSlug)->first();

        return view('blog.show', [
            'post' => $post,
            'content' => $content,
            'comments' => $comments,
            'commentsEnabled' => $commentsEnabled,
            'related' => $related,
            'featuredService' => $featuredService,
            'seo' => $seo,
            'isPreview' => $isPreview,
        ]);
    }

    private function baseQuery(): Builder
    {
        return BlogPost::query()->published()->with(['author', 'category', 'featuredMedia'])->latest('published_at');
    }

    private function archive(Builder $query, string $heading, ?string $intro, string $canonical, ?int $activeCategory = null, ?Author $author = null): View
    {
        $seo = SeoData::make($heading.' — Insights', $intro)
            ->canonical($canonical)
            ->breadcrumbs([['Insights', route('blog.index')], [$heading, null]]);

        return view('blog.index', [
            'posts' => $query->paginate(9)->withQueryString(),
            'featured' => null,
            'search' => '',
            'heading' => $heading,
            'intro' => $intro,
            'categories' => $this->categories(),
            'activeCategory' => $activeCategory,
            'author' => $author,
            'seo' => $seo,
        ]);
    }

    private function categories(): Collection
    {
        return BlogCategory::query()->active()->ordered()->whereHas('posts', fn (Builder $q) => $q->published())->get();
    }
}
