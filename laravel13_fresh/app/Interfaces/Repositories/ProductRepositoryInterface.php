<?php

namespace App\Interfaces\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    /**
     * Retrieve all products with optional filters, relations, pagination, and sorting.
     *
     * @param array $params [
     *     'filters' => [...],
     *     'relations' => [...],
     *     'paginate' => true|null|false,
     *     'perPage' => int|null,
     *     'sortBy' => string|null,
     *     'direction' => 'asc'|'desc',
     * ]
     * @param bool $mapped
     */
    public function all(array $params = [], bool $mapped): LengthAwarePaginator|Collection|SupportCollection;

    /**
     * Find a product by its ID.
     */
    public function find(int $id, array $params = []): ?object;

    /**
     * Find a product by its slug.
     */
    public function findBySlug(string $slug, array $params = []): ?object;

    public function create(array $data): Product;

    public function update(int $id, array $data): Product;

    public function delete(int $id): bool;

    public function mapProduct(Product $product): object;
}
