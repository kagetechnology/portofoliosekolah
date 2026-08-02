<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Portofolio Sekolah] '.$this->contact->subject,
            replyTo: $this->contact->sender_email,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-received',
        );
    }
}
