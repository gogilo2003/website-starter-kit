<?php

namespace Gogilo\Quotes\Services;

use Gogilo\Quotes\Models\Quote;
use Gogilo\Quotes\Mail\NewQuoteAdmin;
use Illuminate\Support\Facades\Mail;

class QuoteRequestService
{
    protected QuoteService $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    public function requestQuote(array $data)
    {
        try {
            $quote = $this->quoteService->createQuote($data);

            $this->sendAdminNotification($quote);
        } catch (\Throwable $th) {
            throw $th;
        }

        return $quote;
    }

    private function sendAdminNotification(Quote $quote): void
    {
        $adminEmail = config('mail.quote_recipient', 'admin@example.com');
        Mail::to($adminEmail)->queue(new NewQuoteAdmin($quote));
    }
}
