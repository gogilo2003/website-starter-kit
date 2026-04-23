<?php

namespace App\Interfaces\Repositories;

use App\Models\Quote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface QuoteRepositoryInterface
{
    public function getAllQuotes(array $params = []): LengthAwarePaginator|Collection|SupportCollection;
    public function getQuoteById(int $id): ?Quote;
    public function getQuoteByCode(string $code, array $params): object|array|null;
    public function createQuote(array $data): Quote;
    public function updateQuote(int $id, array $data): bool;
    public function deleteQuote(int $id): bool;
    public function attachProducts(int $quoteId, array $products): void;
    public function detachProducts(int $quoteId, array $productIds = []): void;
    public function syncProducts(int $quoteId, array $products): void;
    public function getQuotesByStatus(string $status, int $perPage = 15): LengthAwarePaginator;
    public function getRecentQuotes(int $limit = 10): Collection;
    public function searchQuotes(string $search, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function updateStatus(int $id, string $status): bool;
    public function incrementViewCount(int $id): bool;
}
