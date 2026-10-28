<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Crypt;

trait SubmitsPublicForms
{
    /**
     * Adds the anti-spam fields a real browser submission would contain.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function withSpamProtection(array $data, int $secondsAgo = 10): array
    {
        return $data + [
            '_form_token' => Crypt::encryptString((string) now()->subSeconds($secondsAgo)->timestamp),
            config('softec.spam.honeypot_field') => '',
        ];
    }
}
