<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Industry;
use App\Models\JobOpening;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use App\Support\Seo\SeoData;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * XML sitemap index with one sitemap per public content type. Only published,
 * indexable records are listed; nothing is listed when indexing is disabled.
 */
class SitemapController extends Controller
{
    public const CACHE_TTL = 3600;

    /**
     * @return array<int, string>
     */
    public static function types(): array
    {
        return ['pages', 'services', 'products', 'industries', 'case-studies', 'blog', 'careers'];
    }

    public function index(): Response
    {
        $sitemaps = collect(self::types())->map(fn (string $type) => [
            'loc' => route('sitemap.type', $type),
            'lastmod' => optional($this->entries($type)->max('lastmod'))->toAtomString(),
        ]);

        return response()->view('sitemap.index', ['sitemaps' => $sitemaps])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function show(string $type): Response
    {
        abort_unless(in_array($type, self::types(), true), 404);

        return response()->view('sitemap.urlset', ['entries' => $this->entries($type)])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return Collection<int, array{loc: string, lastmod: ?CarbonInterface}>
     */
    private function entries(string $type): Collection
    {
        if (! SeoData::siteAllowsIndexing()) {
            return collect();
        }

        return Cache::remember("sitemap.{$type}", self::CACHE_TTL, function () use ($type): Collection {
            $indexable = fn (Builder $q) => $q->whereDoesntHave('seo', fn (Builder $s) => $s->where('robots_index', false));
            $map = fn ($model) => ['loc' => $model->publicUrl(), 'lastmod' => $model->updated_at];

            return match ($type) {
                'pages' => collect([
                    ['loc' => route('home'), 'lastmod' => Page::query()->where('slug', 'home')->value('updated_at')],
                    ['loc' => route('services.index'), 'lastmod' => Service::query()->published()->max('updated_at')],
                    ['loc' => route('products.index'), 'lastmod' => Product::query()->published()->max('updated_at')],
                    ['loc' => route('industries.index'), 'lastmod' => Industry::query()->published()->max('updated_at')],
                    ['loc' => route('portfolio.index'), 'lastmod' => Portfolio::query()->published()->max('updated_at')],
                    ['loc' => route('blog.index'), 'lastmod' => BlogPost::query()->published()->max('updated_at')],
                    ['loc' => route('contact.create'), 'lastmod' => null],
                    ['loc' => route('project.create'), 'lastmod' => null],
                    ['loc' => route('testimonials.index'), 'lastmod' => null],
                    ['loc' => route('faq.index'), 'lastmod' => null],
                ])->merge(Page::query()->published()->where('slug', '!=', 'home')->where('show_in_sitemap', true)->where($indexable)->get()->map($map))
                    ->merge(BlogCategory::query()->active()->whereHas('posts', fn (Builder $q) => $q->published())->get()->map(fn ($c) => ['loc' => $c->publicUrl(), 'lastmod' => $c->updated_at]))
                    ->merge(Author::query()->active()->whereHas('posts', fn (Builder $q) => $q->published())->get()->map(fn ($a) => ['loc' => $a->publicUrl(), 'lastmod' => $a->updated_at])),
                'services' => Service::query()->published()->where($indexable)->ordered()->get()->map($map),
                'products' => Product::query()->published()->where($indexable)->ordered()->get()->map($map),
                'industries' => Industry::query()->published()->where($indexable)->ordered()->get()->map($map),
                'case-studies' => Portfolio::query()->published()->where($indexable)->latest('published_at')->get()->map($map),
                'blog' => BlogPost::query()->published()->where($indexable)->latest('published_at')->get()->map($map),
                'careers' => settings('features.careers_enabled')
                    ? collect([['loc' => route('careers.index'), 'lastmod' => JobOpening::query()->open()->max('updated_at')]])->merge(JobOpening::query()->open()->where($indexable)->get()->map($map))
                    : collect(),
                default => collect(),
            };
        })->map(fn (array $entry) => ['loc' => $entry['loc'], 'lastmod' => $entry['lastmod'] ? Carbon::parse($entry['lastmod']) : null]);
    }
}
