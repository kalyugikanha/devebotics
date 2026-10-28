<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use App\Support\Html\ContentProcessor;
use App\Support\Seo\SchemaFactory;
use App\Support\Seo\SeoData;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        abort_unless(settings('features.careers_enabled'), 404);

        $jobs = JobOpening::query()->open()->orderByDesc('is_featured')->ordered()->latest('published_at')->get();

        $seo = SeoData::make('Careers', 'Join Softec Ai Solution. Explore open roles in software engineering, AI, cloud, design and more.')
            ->canonical(route('careers.index'))
            ->breadcrumbs([['Careers', null]]);

        return view('careers.index', [
            'jobs' => $jobs,
            'departments' => $jobs->pluck('department')->filter()->unique()->values(),
            'openApplications' => (bool) settings('features.open_applications_enabled'),
            'seo' => $seo,
        ]);
    }

    public function general(): View
    {
        abort_unless(settings('features.careers_enabled') && settings('features.open_applications_enabled'), 404);

        $seo = SeoData::make('Open Application', 'Do not see the right role? Send Softec Ai Solution an open application.')
            ->canonical(route('careers.general'))
            ->breadcrumbs([['Careers', route('careers.index')], ['Open application', null]]);

        return view('careers.apply', ['job' => null, 'seo' => $seo]);
    }

    public function show(string $slug): View
    {
        abort_unless(settings('features.careers_enabled'), 404);

        return $this->render(JobOpening::query()->published()->where('slug', $slug)->firstOrFail());
    }

    public function render(JobOpening $job, bool $isPreview = false): View
    {
        $job->load('seo.ogImage');

        $seo = SeoData::forModel($job, $job->title.' — Careers', $job->summary)
            ->canonical($job->publicUrl())
            ->breadcrumbs([['Careers', route('careers.index')], [$job->title, null]])
            ->withSchema(SchemaFactory::jobPosting($job));

        if ($isPreview || ! $job->isOpen()) {
            $seo->noindex();
        }

        return view('careers.show', [
            'job' => $job,
            'description' => app(ContentProcessor::class)->process($job->description, false)['html'],
            'seo' => $seo,
            'isPreview' => $isPreview,
        ]);
    }
}
