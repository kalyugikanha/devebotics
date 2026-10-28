<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Industry;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use App\Support\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless(settings('features.search_enabled'), 404);

        $term = mb_substr(trim((string) $request->string('q')), 0, 80);
        $groups = collect();

        if (mb_strlen($term) >= 2) {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $match = fn (array $columns) => fn (Builder $q) => $q->where(function (Builder $w) use ($columns, $like): void {
                foreach ($columns as $column) {
                    $w->orWhere($column, 'like', $like);
                }
            });

            $groups = collect([
                'Services' => Service::query()->published()->where($match(['title', 'tagline', 'excerpt', 'overview']))->ordered()->limit(8)->get()->map(fn (Service $s) => [$s->title, $s->excerpt, $s->publicUrl()]),
                'Products' => Product::query()->published()->where($match(['name', 'tagline', 'summary']))->ordered()->limit(8)->get()->map(fn (Product $p) => [$p->name, $p->summary, $p->publicUrl()]),
                'Industries' => Industry::query()->published()->where($match(['name', 'excerpt']))->ordered()->limit(6)->get()->map(fn (Industry $i) => [$i->name, $i->excerpt, $i->publicUrl()]),
                'Case studies' => Portfolio::query()->published()->where($match(['title', 'summary']))->latest('published_at')->limit(6)->get()->map(fn (Portfolio $p) => [$p->title, $p->summary, $p->publicUrl()]),
                'Insights' => BlogPost::query()->published()->where($match(['title', 'excerpt']))->latest('published_at')->limit(8)->get()->map(fn (BlogPost $p) => [$p->title, $p->excerpt, $p->publicUrl()]),
                'Pages' => Page::query()->published()->where('template', '!=', 'home')->where($match(['title', 'excerpt']))->limit(6)->get()->map(fn (Page $p) => [$p->title, $p->excerpt, $p->publicUrl()]),
            ])->map(fn ($items) => $items->map(fn (array $item) => ['title' => $item[0], 'excerpt' => Str::limit((string) $item[1], 160), 'url' => $item[2]]))
                ->filter(fn ($items) => $items->isNotEmpty());
        }

        $seo = SeoData::make($term !== '' ? 'Search results for “'.$term.'”' : 'Search')->noindex();

        return view('search.index', [
            'term' => $term,
            'groups' => $groups,
            'total' => $groups->sum(fn ($items) => $items->count()),
            'seo' => $seo,
        ]);
    }
}
