<?php

namespace Tests\Unit;

use App\Support\Html\ContentSanitizer;
use App\Support\SafeUrl;
use App\Support\Typography;
use Tests\TestCase;

class ContentSanitizerTest extends TestCase
{
    public function test_scripts_handlers_styles_and_dangerous_urls_are_removed(): void
    {
        $dirty = '<h1>Title</h1><p onclick="steal()" style="position:fixed">Hello <a href="javascript:alert(1)">bad</a> <a href="https://example.com">good</a></p>'
            .'<script>alert(1)</script><img src="x" onerror="alert(1)"><iframe src="https://evil.test"></iframe><img src="data:image/png;base64,AAAA">';

        $clean = app(ContentSanitizer::class)->clean($dirty);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('style=', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringNotContainsString('data:image', $clean);
        $this->assertStringNotContainsString('<h1>', $clean);
        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_safe_url_only_allows_relative_and_http_links(): void
    {
        $this->assertSame(url('/contact'), SafeUrl::make('/contact'));
        $this->assertSame('https://example.com', SafeUrl::make('https://example.com'));
        $this->assertNull(SafeUrl::make('javascript:alert(1)'));
        $this->assertNull(SafeUrl::make('//evil.test'));
        $this->assertNull(SafeUrl::make('data:text/html,hi'));
    }

    public function test_heading_highlights_escape_html(): void
    {
        $this->assertSame('Build <span class="text-gradient">AI</span> &lt;b&gt;', (string) Typography::highlight('Build [AI] <b>'));
        $this->assertSame('Build AI', Typography::plain('Build [AI]'));
    }
}
