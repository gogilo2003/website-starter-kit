<?php

namespace Gogilo\Products\Services;

use Gogilo\Products\Repositories\BrandRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BrandService
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository
    ) {}

    public function fetchAllBrands(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->brandRepository->getAllBrands($params);
    }

    public function retrieveBrandById(int $id, array $relations = [])
    {
        return $this->brandRepository->getBrandById($id, $relations);
    }

    public function createNewBrand(array $brandData)
    {
        try {
            $validatedData = $this->validateBrandData($brandData);

            return $this->brandRepository->createBrand($validatedData);
        } catch (\Exception $e) {
            Log::error('Failed to create brand: '.$e->getMessage());
            throw new \Exception('Failed to create brand: '.$e->getMessage());
        }
    }

    public function updateExistingBrand(int $id, array $brandData)
    {
        try {
            $validatedData = $this->validateBrandData($brandData);

            return $this->brandRepository->updateBrand($id, $validatedData);
        } catch (\Exception $e) {
            Log::error('Failed to update brand with ID '.$id.': '.$e->getMessage());
            throw new \Exception('Failed to update brand: '.$e->getMessage());
        }
    }

    public function deleteBrandPermanently(int $id): bool
    {
        try {
            return $this->brandRepository->deleteBrand($id);
        } catch (\Exception $e) {
            Log::error('Failed to delete brand with ID '.$id.': '.$e->getMessage());
            throw new \Exception('Failed to delete brand: '.$e->getMessage());
        }
    }

    public function searchBrandsByName(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->brandRepository->getAllBrands([
            'paginate' => true,
            'per_page' => $perPage,
            'search' => $search,
        ]);
    }

    public function fetchBrandsForDropdown(): array
    {
        return $this->brandRepository->getAllBrandsList();
    }

    public function validateBrandData(array $data): array
    {
        $validated = [];

        if (isset($data['name'])) {
            $validated['name'] = trim($data['name']);
        }

        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $validated['logo'] = $data['logo'];
        }

        return $validated;
    }

    public function fetchBrandsWithStatistics(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->brandRepository->getBrandsWithStats($params);
    }

    public function fetchBrandsWhere(array $conditions, array $params = []): Collection
    {
        return $this->brandRepository->getBrandsWhere($conditions, $params);
    }

    public function fetchBrandsByIds(array $ids, array $params = []): Collection
    {
        return $this->brandRepository->getBrandsByIds($ids, $params);
    }

    public function fetchFeaturedBrands(int $limit = 10): Collection
    {
        return $this->brandRepository->getAllBrands([
            'paginate' => false,
            'limit' => $limit,
            'order_by' => 'name',
            'order_direction' => 'asc',
        ]);
    }

    public function fetchPaginatedBrands(
        int $perPage = 15,
        string $orderBy = 'created_at',
        string $orderDirection = 'desc',
        ?string $search = null
    ): LengthAwarePaginator {
        $params = [
            'paginate' => true,
            'per_page' => $perPage,
            'order_by' => $orderBy,
            'order_direction' => $orderDirection,
        ];

        if ($search) {
            $params['search'] = $search;
        }

        return $this->brandRepository->getAllBrands($params);
    }
}
