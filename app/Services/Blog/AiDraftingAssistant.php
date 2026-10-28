<?php

namespace App\Services\Blog;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Optional AI-assisted outlining for blog editors (Anthropic Messages API).
 * Output is only ever inserted into a draft's body for human editing — it is
 * never published automatically. The API key stays server-side.
 */
class AiDraftingAssistant
{
    public const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    public function isAvailable(): bool
    {
        return (bool) settings('integrations.ai_drafting_enabled')
            && filled(config('softec.ai.anthropic_api_key'));
    }

    /**
     * Returns an HTML outline (converted from Markdown with raw HTML stripped).
     *
     * @throws RuntimeException
     */
    public function outline(string $topic, ?string $notes = null): string
    {
        if (! $this->isAvailable()) {
            throw new RuntimeException('AI drafting is not configured.');
        }

        $company = (string) settings('company.name');

        $prompt = "Write a detailed article outline for the blog of {$company}, an IT services, software development and AI solutions company.\n\n"
            .'Topic: '.Str::limit($topic, 300)."\n"
            .(filled($notes) ? 'Editor notes: '.Str::limit((string) $notes, 1500)."\n" : '')
            ."\nRequirements:\n"
            ."- Use Markdown with H2 (##) and H3 (###) headings and short bullet points under each.\n"
            ."- Start with a one-paragraph introduction draft and end with a short conclusion draft.\n"
            ."- Do not invent statistics, client names, quotes or case studies; mark places needing facts as [Add verified source].\n"
            .'- Do not include an H1 title.';

        try {
            $response = Http::withHeaders([
                'x-api-key' => (string) config('softec.ai.anthropic_api_key'),
                'anthropic-version' => '2023-06-01',
            ])
                ->acceptJson()
                ->timeout(60)
                ->post(self::ENDPOINT, [
                    'model' => (string) config('softec.ai.anthropic_model'),
                    'max_tokens' => 1500,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);
        } catch (Throwable $exception) {
            throw new RuntimeException('The AI service could not be reached. Please try again later.', previous: $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException('The AI service returned an error ('.$response->status().'). Please try again later.');
        }

        $markdown = collect((array) $response->json('content', []))
            ->filter(fn ($block) => is_array($block) && ($block['type'] ?? null) === 'text')
            ->pluck('text')
            ->implode("\n\n");

        if (trim($markdown) === '') {
            throw new RuntimeException('The AI service returned an empty response.');
        }

        return Str::markdown($markdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }
}
