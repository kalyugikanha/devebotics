<?php

namespace App\Listeners;

use App\Enums\EmailStatus;
use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;

class MarkEmailSent
{
    public function handle(MessageSent $event): void
    {
        $header = $event->message->getHeaders()->get('X-Email-Log-Id');

        if ($header === null) {
            return;
        }

        EmailLog::query()->whereKey((int) $header->getBodyAsString())->update([
            'status' => EmailStatus::Sent->value,
            'sent_at' => now(),
            'error' => null,
        ]);
    }
}
