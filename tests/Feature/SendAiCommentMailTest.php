<?php

namespace Tests\Feature;

use App\Mail\SendAiComment;
use Tests\TestCase;

class SendAiCommentMailTest extends TestCase
{
    public function test_it_renders_ai_comment_email(): void
    {
        $mailable = new SendAiComment(
            userName: 'Иван',
            verseReference: 'Иоанна 3:16',
            translation: 'rbo',
            verseUrl: 'https://example.test/rbo/43/3/16',
            commentHtml: '<p>Благодать и истина.</p>',
            model: 'gemini-3.6-flash',
        );

        $html = $mailable->render();

        $this->assertSame(
            'ИИ Библия. Комментарий к стиху Иоанна 3:16',
            $mailable->envelope()->subject
        );
        $this->assertStringContainsString('Иоанна 3:16', $html);
        $this->assertStringContainsString('Благодать и истина.', $html);
        $this->assertStringContainsString('https://example.test/rbo/43/3/16', $html);
        $this->assertStringContainsString('Иван', $html);
        $this->assertStringContainsString('gemini-3.6-flash', $html);
    }

    public function test_it_strips_unsafe_html_from_the_email_comment(): void
    {
        $mailable = new SendAiComment(
            userName: 'Иван',
            verseReference: 'Иоанна 3:16',
            translation: 'rbo',
            verseUrl: 'https://example.test/rbo/43/3/16',
            commentHtml: '<p>Безопасный текст</p><script>alert(1)</script>'
                . '<a href="javascript:alert(1)">ссылка</a>',
        );

        $html = $mailable->render();

        $this->assertStringContainsString('Безопасный текст', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }
}
