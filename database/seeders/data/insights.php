<?php

/*
 * Genuine Insights articles (published as real content, not demo data).
 * Each post lives in data/insights/*.php; this file aggregates them, newest first.
 * Review and adapt with your team before relying on any article as advice.
 */

$posts = array_map(fn (string $file): array => require $file, glob(__DIR__.'/insights/*.php') ?: []);

usort($posts, fn (array $a, array $b): int => $a['days_ago'] <=> $b['days_ago']);

return [
    // New tags introduced by these articles (slug => name). Existing tags are created by CoreContentSeeder.
    'tags' => [
        'custom-software' => 'Custom software',
        'ai-agents' => 'AI agents',
        'saas' => 'SaaS',
        'mobile-apps' => 'Mobile apps',
        'web-development' => 'Web development',
    ],
    'posts' => $posts,
];
