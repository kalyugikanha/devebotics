<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobApplicationRequest;
use App\Models\JobOpening;
use App\Services\JobApplicationService;
use Illuminate\Http\RedirectResponse;

class JobApplicationController extends Controller
{
    public function store(JobApplicationRequest $request, JobApplicationService $applications, ?string $slug = null): RedirectResponse
    {
        abort_unless(settings('features.careers_enabled'), 404);

        $job = null;

        if ($slug !== null) {
            $job = JobOpening::query()->published()->where('slug', $slug)->firstOrFail();
            abort_unless($job->isOpen(), 410, 'This position is no longer accepting applications.');
        } else {
            abort_unless(settings('features.open_applications_enabled'), 404);
        }

        $application = $applications->submit($request->safe()->except(['privacy_consent', 'resume']), $request, $request->file('resume'), $job);

        return redirect()->to($job ? $job->publicUrl().'#apply' : route('careers.general'))
            ->with('application_reference', $application->reference);
    }
}
