<?php

namespace App\Support\Mail;

use App\Enums\EmailStatus;
use App\Mail\TemplatedMail;
use App\Models\EmailLog;
use App\Support\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Queues templated emails and records them in the email log
 * (recipients masked, no message bodies or credentials stored).
 */
class Outbox
{
    /**
     * @param  string|array<int, string>  $recipients
     */
    public function send(string|array $recipients, TemplatedMail $mail, ?Model $related = null): bool
    {
        $recipients = array_values(array_filter((array) $recipients, fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL)));

        if ($recipients === [] || $mail->rendered() === null) {
            return false;
        }

        $sentAny = false;

        foreach ($recipients as $recipient) {
            $log = EmailLog::query()->create([
                'template_key' => $mail->templateKey,
                'mailable' => class_basename($mail),
                'recipient_masked' => AuditLogger::maskEmail($recipient),
                'subject' => $mail->rendered()->subject,
                'status' => EmailStatus::Queued->value,
                'related_type' => $related?->getMorphClass(),
                'related_id' => $related?->getKey(),
            ]);

            $copy = clone $mail;
            $copy->emailLogId = $log->getKey();

            try {
                Mail::to($recipient)->queue($copy);
                $sentAny = true;
            } catch (Throwable $exception) {
                $copy->failed($exception);
                report($exception);
            }
        }

        return $sentAny;
    }
}
