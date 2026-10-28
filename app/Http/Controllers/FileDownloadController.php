<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\JobApplication;
use App\Models\Testimonial;
use App\Support\Audit\AuditLogger;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Authorised downloads of private files (never publicly reachable).
 * Every download is checked against the record's policy and audited.
 */
class FileDownloadController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function inquiryAttachment(ContactInquiry $inquiry): StreamedResponse
    {
        Gate::authorize('downloadAttachment', $inquiry);

        return $this->download($inquiry->attachment_path, $inquiry->attachment_name, $inquiry, 'inquiry.attachment_downloaded');
    }

    public function resume(JobApplication $application): StreamedResponse
    {
        Gate::authorize('downloadResume', $application);

        return $this->download($application->resume_path, $application->resume_name, $application, 'application.resume_downloaded');
    }

    public function testimonialPhoto(Testimonial $testimonial): StreamedResponse
    {
        Gate::authorize('viewPrivatePhoto', $testimonial);

        return $this->download($testimonial->submitted_photo_path, 'testimonial-photo-'.$testimonial->getKey(), $testimonial, 'testimonial.photo_viewed', inline: true);
    }

    private function download(?string $path, ?string $name, $subject, string $event, bool $inline = false): StreamedResponse
    {
        $disk = Storage::disk(config('softec.uploads.private_disk'));

        abort_if(blank($path) || ! $disk->exists($path), 404);

        $this->audit->log($event, $subject, description: 'Private file accessed');

        $filename = $name ?: basename($path);
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; sandbox",
        ];

        return $inline
            ? $disk->response($path, $filename, $headers)
            : $disk->download($path, $filename, $headers);
    }
}
