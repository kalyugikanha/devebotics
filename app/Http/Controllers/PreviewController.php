<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Industry;
use App\Models\JobOpening;
use App\Models\Page;
use App\Models\Portfolio;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Renders unpublished content for administrators. Requires an authenticated
 * session, a valid temporary signature AND view permission for the record.
 * Responses are marked noindex.
 */
class PreviewController extends Controller
{
    public function __invoke(string $type, int $id): View
    {
        [$model, $renderer] = match ($type) {
            'service' => [Service::class, fn ($r) => app(ServiceController::class)->render($r, true)],
            'product' => [Product::class, fn ($r) => app(ProductController::class)->render($r, true)],
            'industry' => [Industry::class, fn ($r) => app(IndustryController::class)->render($r, true)],
            'portfolio' => [Portfolio::class, fn ($r) => app(PortfolioController::class)->render($r, true)],
            'blog_post' => [BlogPost::class, fn ($r) => app(BlogController::class)->render($r, true)],
            'job_opening' => [JobOpening::class, fn ($r) => app(CareerController::class)->render($r, true)],
            'page' => [Page::class, fn ($r) => app(PageController::class)->render($r, true)],
            default => abort(404),
        };

        $record = $model::query()->findOrFail($id);

        Gate::authorize('view', $record);

        return $renderer($record);
    }
}
