<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Services\QuoteService;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteProduct;
use App\Http\Requests\UpdateQuoteRequest;

class QuoteController extends Controller
{
    protected QuoteService $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'search', 'date_from', 'date_to']);
        $perPage = $request->get('per_page', 15);

        $params = [
            'filters' => $filters,
            'per_page' => $perPage,
            'paginate' => true,
            'mapped' => true,
            'relations' => ['items.product'],
        ];

        $quotes = $this->quoteService->getAllQuotes($params);
        $statistics = $this->quoteService->getQuoteStatistics();

        return Inertia::render('Dashboard/Quotes/Index', [
            'quotes' => $quotes,
            'filters' => $filters,
            'statistics' => $statistics,
        ]);
    }

    public function store(StoreQuoteRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $quote = $this->quoteService->createQuote($validated);

            return redirect()->back()->with('success', 'Quote created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create quote: ' . $e->getMessage());
        }
    }

    public function update(UpdateQuoteRequest $request, int $id): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $updated = $this->quoteService->updateQuote($id, $validated);

            if ($updated) {
                return redirect()->back()->with('success', 'Quote updated successfully.');
            }

            return redirect()->back()->with('error', 'Quote not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update quote: ' . $e->getMessage());
        }
    }

    public function updateQuoteItem(UpdateQuoteProduct $request, int $id)
    {
        try {
            $validated = $request->validated();

            $data = [];
            if (!empty($validated["price"])) {
                $data["price"] = $validated["price"];
            }
            if (!empty($validated["quantity"])) {
                $data["quantity"] = $validated["quantity"];
            }
            $updated = $this->quoteService->updateQuoteItem($id, $data);

            if ($updated) {
                return redirect()->back()->with('success', 'Quote Item updated successfully.');
            }

            return redirect()->back()->with('error', 'Quote not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update quote: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->quoteService->deleteQuote($id);

            if ($deleted) {
                return redirect()->back()->with('success', 'Quote deleted successfully.');
            }

            return redirect()->back()->with('error', 'Quote not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete quote: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,sent,viewed,completed,rejected',
        ]);

        try {
            $updated = $this->quoteService->updateQuoteStatus($id, $request->status);

            if ($updated) {
                return redirect()->back()->with('success', 'Quote status updated successfully.');
            }

            return redirect()->back()->with('error', 'Quote not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update quote status.');
        }
    }
}
