<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data, public bool $isAdminCopy = false) {}

    public function envelope(): Envelope
    {
        $subject = $this->isAdminCopy ? "Новое обращение от {$this->data['name']}" : "Ваше обращение принято";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact');
    }
}
