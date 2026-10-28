<?php

namespace App\Http\Controllers;

use App\Enums\ProductAvailability;
use App\Models\Industry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Html\ContentProcessor;
use App\Support\Seo\SchemaFactory;
use App\Support\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'category' => (string) $request->string('category'),
            'industry' => (string) $request->string('industry'),
            'availability' => ProductAvailability::tryFrom((string) $request->string('availability'))?->value ?? '',
            'q' => mb_substr(trim((string) $request->string('q')), 0, 80),
        ];

        $products = Product::query()->published()
            ->with(['category', 'logo', 'heroMedia'])
            ->when($filters['category'] !== '', fn (Builder $q) => $q->whereHas('category', fn (Builder $c) => $c->where('slug', $filters['category'])))
            ->when($filters['industry'] !== '', fn (Builder $q) => $q->whereHas('industries', fn (Builder $i) => $i->where('slug', $filters['industry'])))
            ->when($filters['availability'] !== '', fn (Builder $q) => $q->where('availability', $filters['availability']))
            ->when($filters['q'] !== '', fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('name', 'like', '%'.addcslashes($filters['q'], '%_\\').'%')
                ->orWhere('summary', 'like', '%'.addcslashes($filters['q'], '%_\\').'%')))
            ->orderByDesc('is_featured')->ordered()
            ->paginate(12)
            ->withQueryString();

        $seo = SeoData::make('Products & Software Solutions', 'Browse Softec Ai Solution software products: SaaS, AI-powered applications, CRM and ERP platforms, automation tools and industry-specific solutions.')
            ->canonical(route('products.index'))
            ->breadcrumbs([['Products', null]]);

        if (array_filter($filters) !== []) {
            $seo->noindex();
        }

        return view('products.index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => ProductCategory::query()->active()->ordered()->whereHas('products', fn (Builder $q) => $q->published())->get(),
            'industries' => Industry::query()->published()->ordered()->whereHas('products', fn (Builder $q) => $q->published())->get(),
            'availabilities' => ProductAvailability::cases(),
            'seo' => $seo,
        ]);
    }

    public function show(string $slug): View
    {
        return $this->render(Product::query()->published()->where('slug', $slug)->firstOrFail());
    }

    public function render(Product $product, bool $isPreview = false): View
    {
        $product->load([
            'category', 'logo', 'heroMedia', 'seo.ogImage', 'technologies.logo', 'galleryItems.media',
            'plans' => fn ($q) => $q->where('is_active', true),
            'industries' => fn ($q) => $q->published()->ordered(),
            'services' => fn ($q) => $q->published()->ordered()->limit(4),
            'relatedProducts' => fn ($q) => $q->published()->with(['logo', 'heroMedia', 'category'])->limit(3),
            'portfolios' => fn ($q) => $q->published()->with('heroMedia')->limit(3),
            'faqs' => fn ($q) => $q->where('is_active', true),
        ]);

        $image = $product->heroMedia ?? $product->logo;

        $seo = SeoData::forModel($product, $product->name, $product->summary, $image)
            ->canonical($product->publicUrl())
            ->breadcrumbs([['Products', route('products.index')], [$product->name, null]]);

        $seo->withSchema(SchemaFactory::product($product, $seo->image));

        if ($isPreview) {
            $seo->noindex();
        }

        return view('products.show', [
            'product' => $product,
            'description' => app(ContentProcessor::class)->process($product->description, false)['html'],
            'seo' => $seo,
            'isPreview' => $isPreview,
        ]);
    }
}
