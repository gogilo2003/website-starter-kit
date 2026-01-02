<?php

namespace App\Services;

use App\Models\Quote;
use App\Mail\QuoteCreated;
use App\Mail\NewQuoteAdmin;
use App\Services\QuoteService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuoteRequestService
{
    protected QuoteService $quoteService;
    /**
     * Create a new class instance.
     */
    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    public function requestQuote(array $data)
    {
        try {
            $quote = $this->quoteService->createQuote($data);

            // Optionally, send to admin
            $this->sendAdminNotification($quote);
        } catch (\Throwable $th) {
            throw $th;
        }

        return $quote;
    }
    /**
     * Send notification to admin/team.
     */
    private function sendAdminNotification(Quote $quote): void
    {
        // Send to configured admin email
        $adminEmail = config('mail.quote_recipient', 'admin@example.com');

        // We can use a different mailable for admin
        Mail::to($adminEmail)->queue(new NewQuoteAdmin($quote));
    }
}
