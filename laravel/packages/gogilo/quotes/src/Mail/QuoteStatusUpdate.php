<?php

namespace Gogilo\Quotes\Mail;

use Gogilo\Quotes\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailables\Attachment;

class QuoteStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public Quote $quote;
    public $hasPdfAttachment;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
        $this->hasPdfAttachment = $quote->status === 'completed' && $quote->quote_path;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quote Status Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'quotes::emails.quote-status',
            with: [
                'hasPdfAttachment' => $this->hasPdfAttachment,
            ],
        );
    }

    public function attachments(): array
    {
        $hasPdfAttachment = $this->quote->status === 'completed' && $this->quote->quote_path;
        if (!$hasPdfAttachment) {
            return [];
        }

        if (!Storage::disk()->exists($this->quote->quote_path)) {
            return [];
        }
        return [
            Attachment::fromStorage(Storage::disk('public')->path($this->quote->quote_path))
                ->as('Quote-' . $this->quote->code . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
