<?php

namespace App\Repositories;

use App\Support\Util;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Interfaces\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Collection as SupportCollection;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(array $params = [], bool $mapped): LengthAwarePaginator|Collection|SupportCollection
    {
        $filters = $params['filters'] ?? [];
        $relations = $params['relations'] ?? [];
        $perPage = $params['perPage'] ?? null;
        $sortBy = $params['sortBy'] ?? null;
        $direction = $params['direction'] ?? 'asc';
        $paginate = $params['paginate'] ?? null;

        $query = Product::query()->with($relations);

        if (isset($filters['published'])) {
            $query->where('published', $filters['published']);
        }

        if (isset($filters['category_id']) || isset($filters['category'])) {
            $query->where('product_category_id', $filters['category_id'] ?? $filters['category']);
        }

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if ($sortBy) {
            $query->orderBy($sortBy, $direction);
        }

        if ($paginate || $perPage) {
            $perPage = $perPage ?? 10;
            $results = $query->paginate($perPage);
            return $mapped
                ? $results->through(fn($product) => $this->mapProduct($product))
                : $results;
        }

        $results = $query->get();
        return $mapped
            ? $results->map(fn($product) => $this->mapProduct($product))
            : $results;
    }

    public function find(int $id, array $params = []): ?object
    {
        $relations = $params['relations'] ?? [];
        $mapped = $params['mapped'] ?? true;

        $product = Product::with($relations)->find($id);

        return $mapped && $product ? $this->mapProduct($product) : $product;
    }

    public function findBySlug(string $slug, array $params = []): ?object
    {
        $relations = $params['relations'] ?? [];
        $mapped = $params['mapped'] ?? true;

        $product = Product::with($relations)->where('slug', $slug)->first();

        return $mapped && $product ? $this->mapProduct($product) : $product;
    }

    public function create(array $data): Product
    {
        $product = new Product();
        $product->title = $data['title'];
        $product->slug = $data['slug'];
        $product->summary = $data['summary'] ?? null;
        $product->content = $data['content'];
        $product->price = $data['price'] ?? null;
        $product->features = $data['features'] ?? null;
        $product->published = $data['published'] ?? false;
        $product->front = $data['front'] ?? false;
        $product->product_category_id = $data['category'] ?? null;
        $product->brand_id = $data['brand'] ?? null;
        $product->save();

        return $product;
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->find($id, ['mapped' => false]);
        $product->title = $data['title'] ?? $product->title;
        $product->slug = $data['slug'] ?? $product->slug;
        $product->summary = $data['summary'] ?? $product->summary;
        $product->content = $data['content'] ?? $product->content;
        $product->price = $data['price'] ?? $product->price;
        $product->features = $data['features'] ?? $product->features;
        $product->product_category_id = $data['category'] ?? $product->product_category_id;
        $product->brand_id = $data['brand'] ?? $product->brand_id;
        $product->save();

        return $product;
    }

    public function delete(int $id): bool
    {
        $product = $this->find($id, ['mapped' => false]);
        return $product->delete();
    }

    public function mapProduct(Product $product): object
    {
        return (object) [
            'id' => $product->id,
            'title' => $product->title,
            'slug' => $product->slug,
            'summary' => $product->summary,
            'content' => $product->content,
            'description' => $product->content,
            'price' => $product->price,
            'features' => $product->features,
            'published' => $product->published,
            'front' => $product->front,
            'category' => $product->category?->name,
            'created_at' => $product->created_at,
            'picture' => $product->picture,
            'url' => route('product', $product->slug),
        ];
    }
}
