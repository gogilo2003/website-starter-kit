<?php

namespace Gogilo\Quotes\Repositories;

use Gogilo\Quotes\Interfaces\Repositories\QuoteRepositoryInterface;
use Gogilo\Quotes\Mail\QuoteStatusUpdate;
use Gogilo\Quotes\Models\Quote;
use Gogilo\Quotes\Models\QuoteProduct;
use Gogilo\Quotes\Util\PdfGenerator;
use Gogilo\Products\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuoteRepository implements QuoteRepositoryInterface
{
    public function getAllQuotes(array $params = []): LengthAwarePaginator|Collection|SupportCollection
    {
        $filters = ! empty($params['filters']) ? $params['filters'] : [];

        $query = Quote::query();

        if (! empty($params['relations'])) {
            $query->with($params['relations'])
                ->orderBy('created_at', 'desc');
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $paginate = ! empty($params['paginate']) && $params['paginate'];
        $perPage = ! empty($params['per_page']) ? $params['per_page'] : 10;
        $mapped = ! empty($params['mapped']) && $params['mapped'];
        $res = null;
        if ($paginate) {
            $res = $query->paginate($perPage);

            return $mapped ? $res->through(fn (Quote $quote) => $this->mapQuote($quote)) : $res;
        }
        $res = $query->get();

        return $mapped ? $res->map(fn (Quote $quote) => $this->mapQuote($quote)) : $res;
    }

    public function getQuoteById(int $id): ?Quote
    {
        return Quote::with('items.product')->find($id);
    }

    public function getQuoteByCode(string $code, array $params): array|object|null
    {
        $query = Quote::query();

        if (isset($params['relations'])) {
            $query->with($params['relations']);
        }

        $query->where('code', $code);

        if (isset($params['mapped']) && $params['mapped']) {
            return $this->mapQuote($query->first(), true);
        }

        return $query->first();
    }

    public function createQuote(array $data): Quote
    {
        return DB::transaction(function () use ($data) {
            $quote = Quote::create([
                'code' => $data['code'] ?? Quote::generateUniqueCode(),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'company' => $data['company'] ?? null,
                'message' => $data['message'] ?? null,
                'status' => $data['status'] ?? 'pending',
            ]);

            if (! empty($data['products'])) {
                $this->attachProducts($quote->id, $data['products']);
            }

            return $quote->load('products');
        });
    }

    public function updateQuote(int $id, array $data): bool
    {
        $quote = $this->getQuoteById($id);

        if (! $quote) {
            return false;
        }

        return DB::transaction(function () use ($quote, $data) {
            $updated = $quote->update([
                'name' => $data['name'] ?? $quote->name,
                'email' => $data['email'] ?? $quote->email,
                'phone' => $data['phone'] ?? $quote->phone,
                'company' => $data['company'] ?? $quote->company,
                'message' => $data['message'] ?? $quote->message,
                'status' => $data['status'] ?? $quote->status,
            ]);

            if ($updated && ! empty($data['products'])) {
                $this->syncProducts($quote->id, $data['products']);
            }

            return $updated;
        });
    }

    public function updateItem(int $id, array $data)
    {
        $item = QuoteProduct::find($id);
        $item->quantity = $data['quantity'] ?? $item->quantity;
        $item->price = $data['price'] ?? $item->price;
        $item->notes = $data['notes'] ?? $item->notes;

        return $item->save();
    }

    public function deleteQuote(int $id): bool
    {
        $quote = $this->getQuoteById($id);

        if (! $quote) {
            return false;
        }

        return DB::transaction(function () use ($quote) {
            $quote->products()->detach();

            return $quote->delete();
        });
    }

    public function attachProducts(int $quoteId, array $products): void
    {
        $quote = $this->getQuoteById($quoteId);

        if ($quote) {
            foreach ($products as $product) {
                $quote->products()->attach($product['product_id'], [
                    'quantity' => $product['quantity'] ?? 1,
                    'price' => $product['price'] ?? 0,
                    'notes' => $product['notes'] ?? null,
                ]);
            }
        }
    }

    public function detachProducts(int $quoteId, array $productIds = []): void
    {
        $quote = $this->getQuoteById($quoteId);

        if ($quote) {
            if (empty($productIds)) {
                $quote->products()->detach();
            } else {
                $quote->products()->detach($productIds);
            }
        }
    }

    public function syncProducts(int $quoteId, array $products): void
    {
        $quote = $this->getQuoteById($quoteId);

        if ($quote) {
            $syncData = [];
            foreach ($products as $product) {
                $syncData[$product['product_id']] = [
                    'quantity' => $product['quantity'] ?? 1,
                    'price' => $product['price'] ?? 0,
                    'notes' => $product['notes'] ?? null,
                ];
            }

            $quote->products()->sync($syncData);
        }
    }

    public function getQuotesByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return Quote::with('products')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getRecentQuotes(int $limit = 10): Collection
    {
        return Quote::with('products')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function searchQuotes(string $search, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Quote::with('products')
            ->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $quote = $this->getQuoteById($id);

        if (! $quote) {
            return false;
        }

        if ($status == 'completed') {
            $path = app()->make(PdfGenerator::class)->saveQuotePdf($quote);
            $quote->quote_path = $path;
            $quote->save();
        }

        $res = $quote->update(['status' => $status]);

        Mail::to($quote->email)->queue(new QuoteStatusUpdate($quote));

        return $res;
    }

    public function incrementViewCount(int $id): bool
    {
        $quote = $this->getQuoteById($id);

        if (! $quote) {
            return false;
        }

        $quote->increment('view_count');
        $quote->last_viewed_at = now();

        return $quote->save();
    }

    public function updateLastView(int $id): bool
    {
        $quote = $this->getQuoteById($id);
        $quote->last_viewed_at = now();

        return $quote->save();
    }

    public function mapQuote(Quote $quote, $object = false): array|object
    {
        $data = [
            'id' => $quote->id,
            'code' => $quote->code,
            'name' => $quote->name,
            'email' => $quote->email,
            'phone' => $quote->phone,
            'company' => $quote->company,
            'message' => $quote->message,
            'status' => $quote->status,
            'view_count' => $quote->view_count,
            'last_viewed_at' => $quote->last_viewed_at,
            'total_amount' => $quote->total_amount,
            'created_at' => $quote->created_at,
            'updated_at' => $quote->updated_at,
            'deleted_at' => $quote->deleted_at,
            'quote_path' => $quote->quote_path,
        ];

        if ($quote->relationLoaded('items')) {
            $data['items'] = $quote->items->map(function (QuoteProduct $item) use ($object) {
                $arrayItem = [
                    'id' => $item->id,
                    'quote_id' => $item->quote_id,
                    'product_id' => $item->product_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'notes' => $item->notes,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
                if ($item->relationLoaded('product')) {
                    $arrayItem['product'] = $this->mapProduct($item->product);
                }

                return $object ? (object) $arrayItem : $arrayItem;
            });
        }
        if (! $object) {
            return $data;
        }

        return (object) $data;
    }

    protected function mapProduct(Product $product, bool $object = false)
    {
        $data = [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'summary' => $product->summary,
            'content' => $product->content,
            'price' => $product->price,
            'features' => $product->features,
            'product_category_id' => $product->product_category_id,
            'brand_id' => $product->brand_id,
            'published' => $product->published,
            'front' => $product->front,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'picture' => $product->picture,
        ];
        if (! $object) {
            return $data;
        }

        return (object) $data;
    }
}
