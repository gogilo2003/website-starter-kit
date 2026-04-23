<?php

namespace App\Interfaces\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\ProductCategory;

interface ProductCategoryRepositoryInterface
{
    /**
     * Retrieve all product categories with optional filters, sorting, relations, and pagination.
     *
     * @param  array  $params
     * @param  bool  $mapped
     * @return LengthAwarePaginator|Collection
     */
    public function all(array $params = [], bool $mapped = false): LengthAwarePaginator|Collection;

    /**
     * Retrieve a product category by its ID.
     *
     * @param  int  $id
     * @param  bool  $mapped
     * @param  array  $relations
     * @return ProductCategory|null
     */
    public function getById(int $id, bool $mapped = false, array $relations = []): ?ProductCategory;

    /**
     * Retrieve a product category by its slug.
     *
     * @param  string  $slug
     * @param  bool  $mapped
     * @param  array  $relations
     * @return ProductCategory|null
     */
    public function getBySlug(string $slug, bool $mapped = false, array $relations = []): ?ProductCategory;

    /**
     * Create a new product category.
     *
     * @param  array  $data
     * @return ProductCategory
     */
    public function create(array $data): ProductCategory;

    /**
     * Update an existing product category.
     *
     * @param  int  $id
     * @param  array  $data
     * @return ProductCategory
     */
    public function update(int $id, array $data): ProductCategory;

    /**
     * Delete a product category.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Publish or unpublish a category.
     *
     * @param  int  $id
     * @param  bool  $status
     * @return bool
     */
    public function publish(int $id): bool;

    /**
     * Promote or demote a category.
     *
     * @param  int  $id
     * @param  bool  $status
     * @return bool
     */
    public function promote(int $id): bool;
}
