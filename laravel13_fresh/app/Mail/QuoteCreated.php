<?php

namespace App\Mail;

use App\Models\Quote;
use App\Util\PdfGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class QuoteCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Quote $quote;
    protected PdfGenerator $pdfGenerator;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
        $this->pdfGenerator = new PdfGenerator();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quote Request Confirmation - ' . $this->quote->code,
        );
    }

    public function content(): Content
    {

        return new Content(
            view: 'emails.quote-created',
            with: [
                'quote' => $this->quote,
                'trackingUrl' => $this->quote->tracking_url, // This uses your getTrackingUrlAttribute
                'hasProducts' => $this->quote->products->count() > 0,
            ],
        );
    }

    public function attachments(): array
    {
        if ($this->quote->status !== "completed") {
            return [];
        }
        try {
            $dompdf = $this->pdfGenerator->generateQuotePdf($this->quote);
            $pdfContent = $dompdf->output();
            return [
                Attachment::fromData(fn() => $pdfContent, "quote-{$this->quote->code}.pdf")
                    ->withMime('application/pdf'),
            ];
        } catch (\Exception $e) {
            // Log error but don't fail the email
            Log::error('PDF generation failed: ' . $e->getMessage());
            return [];
        }
    }
}
