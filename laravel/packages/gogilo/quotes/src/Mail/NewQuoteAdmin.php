<?php

namespace Gogilo\Quotes\Mail;

use Gogilo\Quotes\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class NewQuoteAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Quote Request Received - ' . $this->quote->code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'quotes::emails.quote-admin',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
