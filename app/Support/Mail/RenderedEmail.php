<?php

namespace App\Support\Mail;

final readonly class RenderedEmail
{
    public function __construct(
        public string $subject,
        public string $heading,
        public string $bodyHtml,
        public ?string $actionLabel,
    ) {}
}
