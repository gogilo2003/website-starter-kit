<?php

namespace Gogilo\Products\Services;

use Gogilo\Products\Models\ProductCategory;
use Gogilo\Products\Repositories\ProductCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductCategoryService
{
    protected ProductCategoryRepositoryInterface $productCategoryRepository;

    public function __construct(ProductCategoryRepositoryInterface $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public function getAllProductCategories(array $params = [], bool $mapped = false): LengthAwarePaginator|Collection
    {
        return $this->productCategoryRepository->all($params, $mapped);
    }

    public function getProductCategoryById(int $id, bool $mapped = false, array $relations = []): ?ProductCategory
    {
        return $this->productCategoryRepository->getById($id, $mapped, $relations);
    }

    public function getProductCategoryBySlug(string $slug, bool $mapped = false, array $relations = []): ?ProductCategory
    {
        return $this->productCategoryRepository->getBySlug($slug, $mapped, $relations);
    }

    public function createProductCategory(array $data): ProductCategory
    {
        return $this->productCategoryRepository->create($data);
    }

    public function updateProductCategory(int $id, array $data): ProductCategory
    {
        return $this->productCategoryRepository->update($id, $data);
    }

    public function deleteProductCategory(int $id): bool
    {
        return $this->productCategoryRepository->delete($id);
    }

    public function publishProductCategory(int $id): bool
    {
        return $this->productCategoryRepository->publish($id);
    }

    public function promoteProductCategory(int $id): bool
    {
        return $this->productCategoryRepository->promote($id);
    }
}
