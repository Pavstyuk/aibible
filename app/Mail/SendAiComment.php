<?php

namespace App\Mail;

use App\Support\HtmlSanitizer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendAiComment extends Mailable
{
    use Queueable, SerializesModels;

    public string $commentHtml;

    public function __construct(
        public string $userName,
        public string $verseReference,
        public string $translation,
        public string $verseUrl,
        string $commentHtml,
        public string $model = '',
    ) {
        // The controller already sanitises the AI result. Sanitise it once more
        // here so that this mailable remains safe when used from other places.
        $this->commentHtml = HtmlSanitizer::clean($commentHtml);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ИИ Библия. Комментарий к стиху ' . $this->verseReference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.send-ai-comment',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
