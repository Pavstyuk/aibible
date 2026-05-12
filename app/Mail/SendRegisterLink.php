<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendRegisterLink extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $reg_link;


    /**
     * Create a new message instance.
     */
    public function __construct(string $reg_link)
    {

        $this->reg_link = $reg_link;
    }

    public function build()
    {
        return $this->subject('👋 ИИ Библия. Ссылка на завершение регистрации')
            ->view('emails.send-reg-link');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ИИ Библия. Ссылка на завершение регистрации',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send-reg-link',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
