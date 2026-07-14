<?php

namespace Gogilo\Products\Repositories;

use Gogilo\Products\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface ProductRepositoryInterface
{
    public function all(array $params, bool $mapped): LengthAwarePaginator|Collection|SupportCollection;

    public function find(int $id, array $params = []): ?object;

    public function findBySlug(string $slug, array $params = []): ?object;

    public function create(array $data): Product;

    public function update(int $id, array $data): Product;

    public function delete(int $id): bool;

    public function mapProduct(Product $product): object;
}
