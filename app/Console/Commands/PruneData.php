<?php

namespace App\Console\Commands;

use App\Enums\InquiryStatus;
use App\Models\AuditLog;
use App\Models\ContactInquiry;
use App\Models\EmailLog;
use App\Models\JobApplication;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * Enforces the data-retention policy (Settings › Forms, privacy & features
 * and config/softec.php). Personal data is deleted together with its
 * private files. Runs nightly from the scheduler.
 */
class PruneData extends Command
{
    protected $signature = 'softec:prune-data {--dry-run : Report what would be deleted without deleting}';

    protected $description = 'Delete personal data and logs that are older than the configured retention periods';

    private const SOFT_DELETE_GRACE_DAYS = 30;

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $retention = (array) config('softec.retention');
        $results = [];

        $results['Audit log entries'] = $this->prune(
            AuditLog::query()->where('is_security_event', false)->where('created_at', '<', now()->subDays((int) ($retention['audit_logs'] ?? 365))),
            $dryRun,
        );

        $results['Security log entries'] = $this->prune(
            AuditLog::query()->where('is_security_event', true)->where('created_at', '<', now()->subDays((int) ($retention['security_logs'] ?? 180))),
            $dryRun,
        );

        $results['Email log entries'] = $this->prune(
            EmailLog::query()->where('created_at', '<', now()->subDays((int) ($retention['email_logs'] ?? 180))),
            $dryRun,
        );

        $inquiryDays = max(30, (int) settings('privacy.inquiry_retention_days', 730));

        $results['Spam inquiries'] = $this->pruneWithFiles(
            ContactInquiry::withTrashed()->where('is_spam', true)->where('created_at', '<', now()->subDays((int) ($retention['spam_inquiries'] ?? 30))),
            'attachment_path',
            $dryRun,
        );

        $results['Closed inquiries past retention'] = $this->pruneWithFiles(
            ContactInquiry::withTrashed()
                ->whereIn('status', InquiryStatus::values(InquiryStatus::closed()))
                ->where(fn ($q) => $q->where('closed_at', '<', now()->subDays($inquiryDays))
                    ->orWhere(fn ($w) => $w->whereNull('closed_at')->where('created_at', '<', now()->subDays($inquiryDays)))),
            'attachment_path',
            $dryRun,
        );

        $results['Deleted inquiries (grace period over)'] = $this->pruneWithFiles(
            ContactInquiry::onlyTrashed()->where('deleted_at', '<', now()->subDays(self::SOFT_DELETE_GRACE_DAYS)),
            'attachment_path',
            $dryRun,
        );

        $applicationDays = max(30, (int) settings('privacy.application_retention_days', 365));

        $results['Job applications past retention'] = $this->pruneWithFiles(
            JobApplication::withTrashed()->where('created_at', '<', now()->subDays($applicationDays)),
            'resume_path',
            $dryRun,
        );

        $results['Deleted job applications (grace period over)'] = $this->pruneWithFiles(
            JobApplication::onlyTrashed()->where('deleted_at', '<', now()->subDays(self::SOFT_DELETE_GRACE_DAYS)),
            'resume_path',
            $dryRun,
        );

        $this->table(['Data', $dryRun ? 'Would delete' : 'Deleted'], collect($results)->map(fn (int $count, string $label) => [$label, $count])->values()->all());

        return self::SUCCESS;
    }

    private function prune(Builder $query, bool $dryRun): int
    {
        return $dryRun ? $query->count() : $query->delete();
    }

    private function pruneWithFiles(Builder $query, string $fileColumn, bool $dryRun): int
    {
        if ($dryRun) {
            return $query->count();
        }

        $disk = Storage::disk(config('softec.uploads.private_disk'));
        $deleted = 0;

        $query->chunkById(200, function ($records) use ($disk, $fileColumn, &$deleted): void {
            foreach ($records as $record) {
                if (filled($record->{$fileColumn})) {
                    $disk->delete($record->{$fileColumn});
                }

                $record->forceDelete();
                $deleted++;
            }
        });

        return $deleted;
    }
}
