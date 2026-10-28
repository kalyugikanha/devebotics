<?php

namespace App\Support\Mail;

use App\Models\EmailTemplate;
use Illuminate\Support\Str;

/**
 * Renders an email template with whitelisted placeholders.
 *
 * Markdown is converted BEFORE placeholder substitution (placeholders are
 * swapped for opaque tokens first), so user-supplied values such as a
 * visitor's name can never inject links, images or HTML.
 */
class EmailTemplateRenderer
{
    /**
     * @param  array<string, scalar|null>  $variables
     */
    public function render(string $key, array $variables): ?RenderedEmail
    {
        $template = EmailTemplate::query()->where('key', $key)->first();
        $defaults = EmailTemplateRegistry::all()[$key] ?? null;

        if ($template !== null && ! $template->is_active) {
            return null;
        }

        if ($template === null && $defaults === null) {
            return null;
        }

        $subject = $template->subject ?? $defaults['subject'];
        $heading = $template->heading ?? $defaults['heading'] ?? '';
        $body = $template->body ?? $defaults['body'];
        $actionLabel = $template !== null ? $template->action_label : ($defaults['action_label'] ?? null);

        $variables = array_merge(['company_name' => (string) settings('company.name')], $variables);
        $allowed = array_merge(EmailTemplateRegistry::variables($key), ['company_name']);
        $variables = array_intersect_key($variables, array_flip($allowed));

        return new RenderedEmail(
            subject: Str::limit($this->replacePlain($subject, $variables), 200, ''),
            heading: $this->replacePlain($heading, $variables),
            bodyHtml: $this->renderBody($body, $variables),
            actionLabel: $actionLabel ? $this->replacePlain($actionLabel, $variables) : null,
        );
    }

    /**
     * Plain-text substitution for subject/heading (escaped later by Blade).
     *
     * @param  array<string, scalar|null>  $variables
     */
    private function replacePlain(string $text, array $variables): string
    {
        return (string) preg_replace_callback('/\{\{\s*([a-z_]+)\s*\}\}/', function (array $match) use ($variables): string {
            return array_key_exists($match[1], $variables) ? $this->stringify($variables[$match[1]]) : '';
        }, $text);
    }

    /**
     * @param  array<string, scalar|null>  $variables
     */
    private function renderBody(string $markdown, array $variables): string
    {
        $tokens = [];

        $tokenized = (string) preg_replace_callback('/\{\{\s*([a-z_]+)\s*\}\}/', function (array $match) use (&$tokens): string {
            $token = 'TPLVAR'.count($tokens).'X'.Str::random(8);
            $tokens[$token] = $match[1];

            return $token;
        }, $markdown);

        $html = Str::markdown($tokenized, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        foreach ($tokens as $token => $name) {
            $value = array_key_exists($name, $variables) ? $this->stringify($variables[$name]) : '';
            $html = str_replace($token, nl2br(e($value)), $html);
        }

        return $html;
    }

    private function stringify(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return (string) $value;
    }
}
