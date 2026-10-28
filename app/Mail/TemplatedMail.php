<?php

namespace App\Mail;

use App\Enums\EmailStatus;
use App\Models\EmailLog;
use App\Support\Mail\EmailTemplateRenderer;
use App\Support\Mail\RenderedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Throwable;

/**
 * Branded transactional email rendered from an admin-editable template.
 * Sent via App\Support\Mail\Outbox, which records delivery in email_logs.
 */
class TemplatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [60, 300];

    public ?int $emailLogId = null;

    private ?RenderedEmail $rendered = null;

    /**
     * @param  array<string, scalar|null>  $variables
     */
    public function __construct(
        public string $templateKey,
        public array $variables = [],
        public ?string $actionUrl = null,
        public ?string $replyToAddress = null,
    ) {}

    public function rendered(): ?RenderedEmail
    {
        return $this->rendered ??= app(EmailTemplateRenderer::class)->render($this->templateKey, $this->variables);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->rendered()?->subject ?? (string) settings('company.name'),
            replyTo: $this->replyToAddress ? [$this->replyToAddress] : [],
            tags: ['template:'.$this->templateKey],
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: array_filter([
            'X-Template-Key' => $this->templateKey,
            'X-Email-Log-Id' => $this->emailLogId ? (string) $this->emailLogId : null,
        ]));
    }

    public function content(): Content
    {
        $rendered = $this->rendered();

        return new Content(
            view: 'emails.templated',
            with: [
                'heading' => $rendered?->heading,
                'bodyHtml' => $rendered?->bodyHtml ?? '',
                'actionLabel' => $rendered?->actionLabel,
                'actionUrl' => $this->actionUrl,
            ],
        );
    }

    /**
     * Called by the queue after the final failed attempt.
     */
    public function failed(Throwable $exception): void
    {
        if ($this->emailLogId === null) {
            return;
        }

        EmailLog::query()->whereKey($this->emailLogId)->update([
            'status' => EmailStatus::Failed->value,
            // Exception class + truncated message only; transports never include credentials here.
            'error' => Str::limit(class_basename($exception).': '.$exception->getMessage(), 480),
        ]);
    }
}
