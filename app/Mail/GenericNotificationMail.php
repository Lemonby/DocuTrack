<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subjectLine;

    public $messageBody;

    public $ctaUrl;

    public $ctaText;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subjectLine, string $messageBody, ?string $ctaUrl = null, ?string $ctaText = null)
    {
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
        $this->ctaUrl = $ctaUrl;
        $this->ctaText = $ctaText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.generic_notification',
            with: [
                'messageBody' => $this->messageBody,
                'ctaUrl' => $this->ctaUrl,
                'ctaText' => $this->ctaText,
            ],
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
