<?php

namespace App\Services;

use App\Interfaces\Repositories\ProductCategoryRepositoryInterface;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductCategoryService
{
    protected ProductCategoryRepositoryInterface $productCategoryRepository;

    public function __construct(ProductCategoryRepositoryInterface $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    /**
     * Get all product categories.
     */
    public function getAllProductCategories(array $params = [], bool $mapped = false): LengthAwarePaginator|Collection
    {
        return $this->productCategoryRepository->all($params, $mapped);
    }

    /**
     * Get a single product category by ID.
     */
    public function getProductCategoryById(int $id, bool $mapped = false, array $relations = []): ?ProductCategory
    {
        return $this->productCategoryRepository->getById($id, $mapped, $relations);
    }

    /**
     * Get a product category by its slug.
     */
    public function getProductCategoryBySlug(string $slug, bool $mapped = false, array $relations = []): ?ProductCategory
    {
        return $this->productCategoryRepository->getBySlug($slug, $mapped, $relations);
    }

    /**
     * Create a new product category.
     */
    public function createProductCategory(array $data): ProductCategory
    {
        return $this->productCategoryRepository->create($data);
    }

    /**
     * Update an existing product category.
     */
    public function updateProductCategory(int $id, array $data): ProductCategory
    {
        return $this->productCategoryRepository->update($id, $data);
    }

    /**
     * Delete a product category.
     */
    public function deleteProductCategory(int $id): bool
    {
        return $this->productCategoryRepository->delete($id);
    }

    /**
     * Publish or unpublish a product category.
     * @param int $id
     *
     * @return bool
     */
    public function publishProductCategory(int $id): bool
    {
        return $this->productCategoryRepository->publish($id);
    }

    /**
     * Promote or demote a product category.
     * @param int $id
     *
     * @return bool
     */
    public function promoteProductCategory(int $id): bool
    {
        return $this->productCategoryRepository->promote($id);
    }
}
