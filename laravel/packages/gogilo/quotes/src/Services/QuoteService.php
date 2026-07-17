<?php

namespace Gogilo\Quotes\Services;

use Gogilo\Quotes\Models\Quote;
use Gogilo\Quotes\Mail\QuoteCreated;
use Gogilo\Quotes\Util\PdfGenerator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Gogilo\Quotes\Interfaces\Repositories\QuoteRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuoteService
{
    protected QuoteRepositoryInterface $quoteRepository;

    public function __construct(QuoteRepositoryInterface $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    public function getAllQuotes(array $params = []): LengthAwarePaginator|Collection|SupportCollection
    {
        return $this->quoteRepository->getAllQuotes($params);
    }

    public function getQuoteById(int $id): ?Quote
    {
        return $this->quoteRepository->getQuoteById($id);
    }

    public function getQuoteByCode(string $code, array $params): array|object|null
    {
        return $this->quoteRepository->getQuoteByCode($code, $params);
    }

    public function createQuote(array $data, bool $sendEmail = true): Quote
    {
        $quote = $this->quoteRepository->createQuote($data);

        if ($sendEmail && !empty($data['email'])) {
            $this->sendQuoteEmail($quote);
        }

        return $quote;
    }

    public function updateQuote(int $id, array $data): bool
    {
        return $this->quoteRepository->updateQuote($id, $data);
    }

    public function updateQuoteItem(int $id, array $data)
    {
        return $this->quoteRepository->updateItem($id, $data);
    }

    public function deleteQuote(int $id): bool
    {
        return $this->quoteRepository->deleteQuote($id);
    }

    public function updateQuoteStatus(int $id, string $status): bool
    {
        $result = $this->quoteRepository->updateStatus($id, $status);
        return $result;
    }

    public function updateLastView(int $id): bool
    {
        return $this->quoteRepository->incrementViewCount($id);
    }

    public function sendQuoteEmail(Quote $quote): void
    {
        try {
            Mail::to($quote->email)->send(new QuoteCreated($quote));
        } catch (\Exception $e) {
            Log::error('Failed to send quote email: ' . $e->getMessage());
        }
    }

    public function generateQuotePdf(Quote $quote): object
    {
        $pdfGenerator = new PdfGenerator();
        return $pdfGenerator->generateQuotePdf($quote);
    }

    public function downloadQuotePdf(string $code): ?array
    {
        $quote = $this->getQuoteByCode($code, []);

        if (!$quote) {
            return null;
        }

        $this->quoteRepository->incrementViewCount($quote->id);

        if (
            $quote->quote_path &&
            Storage::disk('public')->exists($quote->quote_path)
        ) {
            return [
                'content' => Storage::disk('public')->get($quote->quote_path),
                'filename' => "quote-{$quote->code}.pdf",
                'quote' => $quote,
            ];
        }

        $pdf = $this->generateQuotePdf($quote);

        $pdfContent = $pdf->render();

        $path = "quotes/quote-{$quote->code}.pdf";
        Storage::put($path, $pdfContent);

        $quote->update([
            'quote_path' => $path,
        ]);

        return [
            'content' => $pdfContent,
            'filename' => "quote-{$quote->code}.pdf",
            'quote' => $quote,
        ];
    }

    public function getQuotesByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->quoteRepository->getQuotesByStatus($status, $perPage);
    }

    public function getRecentQuotes(int $limit = 10): Collection
    {
        return $this->quoteRepository->getRecentQuotes($limit);
    }

    public function searchQuotes(string $search, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->quoteRepository->searchQuotes($search, $filters, $perPage);
    }

    public function getQuoteStatistics(): array
    {
        $total = Quote::count();
        $pending = Quote::where('status', 'pending')->count();
        $viewed = Quote::where('status', 'viewed')->count();
        $sent = Quote::where('status', 'sent')->count();
        $completed = Quote::where('status', 'completed')->count();
        $rejected = Quote::where('status', 'rejected')->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'viewed' => $viewed,
            'sent' => $sent,
            'completed' => $completed,
            'rejected' => $rejected,
            'conversion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}
