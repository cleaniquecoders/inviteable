<?php

declare(strict_types=1);

namespace CleaniqueCoders\Inviteable\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'inviteable::mails.invitation',
            with: [
                'url' => route('invitation', $this->token),
            ],
        );
    }
}
