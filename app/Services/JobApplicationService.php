<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\ConsentType;
use App\Mail\TemplatedMail;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Support\Forms\ConsentRecorder;
use App\Support\Forms\SpamGuard;
use App\Support\Mail\Outbox;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobApplicationService
{
    public function __construct(
        private ConsentRecorder $consents,
        private Outbox $outbox,
        private StaffNotifier $notifier,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function submit(array $data, Request $request, UploadedFile $resume, ?JobOpening $job = null): JobApplication
    {
        $application = DB::transaction(function () use ($data, $request, $resume, $job): JobApplication {
            $application = JobApplication::query()->create([
                'job_opening_id' => $job?->getKey(),
                'name' => $data['name'],
                'email' => Str::lower((string) $data['email']),
                'phone' => $data['phone'] ?? null,
                'location' => $data['location'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'portfolio_url' => $data['portfolio_url'] ?? null,
                'cover_letter' => $data['cover_letter'] ?? null,
                'status' => ApplicationStatus::New,
                'ip_hash' => SpamGuard::hashIp($request->ip()),
            ]);

            $application->forceFill([
                'resume_path' => $resume->store('resumes/'.now()->format('Y/m'), config('softec.uploads.private_disk')),
                'resume_name' => Str::limit(basename($resume->getClientOriginalName()), 200, ''),
            ])->save();

            $this->consents->record(ConsentType::RecruitmentData, true, $application, $request);

            return $application;
        });

        $position = $job?->title ?? 'Open application';
        $adminUrl = rescue(fn () => route('filament.admin.resources.job-applications.view', $application), null, false);

        $this->notifier->notify(
            permission: 'applications.view',
            title: 'New application: '.$position,
            body: $application->name.' — '.$application->reference,
            adminUrl: $adminUrl,
            configuredRecipients: (array) settings('forms.careers_recipients', []),
            mail: new TemplatedMail('job_application_notification', [
                'reference' => $application->reference,
                'name' => $application->name,
                'position' => $position,
            ], $adminUrl),
            related: $application,
        );

        if (settings('forms.send_acknowledgements', true)) {
            $this->outbox->send($application->email, new TemplatedMail('job_application_acknowledgement', [
                'name' => $application->name,
                'position' => $position,
                'reference' => $application->reference,
            ], route('careers.index')), $application);
        }

        return $application;
    }
}
