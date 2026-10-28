<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $query = Testimonial::query()->publiclyVisible();

        $summary = [
            'count' => (clone $query)->count(),
            'average' => round((float) (clone $query)->avg('rating'), 1),
        ];

        $testimonials = $query->with(['photo', 'service'])->orderByDesc('is_featured')->ordered()->latest('id')->paginate(12);

        // Note: no AggregateRating markup — Google does not allow self-serving review snippets for an organisation's own reviews.
        $seo = SeoData::make('Client Reviews & Testimonials', 'Read verified feedback from Softec Ai Solution clients. Every review is moderated and published only with the client’s permission.')
            ->canonical(route('testimonials.index'))
            ->breadcrumbs([['Client reviews', null]]);

        return view('testimonials.index', compact('testimonials', 'summary', 'seo'));
    }
}
