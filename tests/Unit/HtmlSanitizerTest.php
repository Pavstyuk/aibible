<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_removes_scripts_event_handlers_and_unsafe_urls(): void
    {
        $html = '<p onclick="alert(1)">Hello <strong>world</strong>'
            . '<script>alert(1)</script>'
            . '<img src=x onerror=alert(1)>'
            . '<a href="javascript:alert(1)">bad</a>'
            . '<a href="https://example.com" title="ok">good</a></p>';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringContainsString('<p>Hello <strong>world</strong>', $clean);
        $this->assertStringNotContainsString('script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $clean);
    }

    public function test_it_keeps_safe_formatting_and_utf8(): void
    {
        $html = '<h3>Заголовок</h3><p>Текст <em>курсив</em><br>и <code>код</code></p>';

        $this->assertSame($html, HtmlSanitizer::clean($html));
    }

    public function test_it_removes_unknown_tags_but_keeps_their_text(): void
    {
        $clean = HtmlSanitizer::clean('<custom><p>Безопасный текст</p></custom>');

        $this->assertStringContainsString('Безопасный текст', $clean);
        $this->assertStringNotContainsString('custom', $clean);
    }
}
