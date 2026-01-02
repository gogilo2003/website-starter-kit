<?php

namespace App\Services;

use App\Interfaces\Repositories\BrandRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BrandService
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository
    ) {}

    /**
     * Fetch all brands with comprehensive filtering
     */
    public function fetchAllBrands(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->brandRepository->getAllBrands($params);
    }

    /**
     * Retrieve a specific brand by ID with optional relations
     */
    public function retrieveBrandById(int $id, array $relations = [])
    {
        return $this->brandRepository->getBrandById($id, $relations);
    }

    /**
     * Create a new brand with eloquent expression
     */
    public function createNewBrand(array $brandData)
    {
        try {
            $validatedData = $this->validateBrandData($brandData);
            return $this->brandRepository->createBrand($validatedData);
        } catch (\Exception $e) {
            Log::error('Failed to create brand: ' . $e->getMessage());
            throw new \Exception('Failed to create brand: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing brand with eloquent expression
     */
    public function updateExistingBrand(int $id, array $brandData)
    {
        try {
            $validatedData = $this->validateBrandData($brandData);
            return $this->brandRepository->updateBrand($id, $validatedData);
        } catch (\Exception $e) {
            Log::error('Failed to update brand: ' . $e->getMessage());
            throw new \Exception('Failed to update brand: ' . $e->getMessage());
        }
    }

    /**
     * Delete a brand with eloquent expression
     */
    public function deleteBrandPermanently(int $id): bool
    {
        try {
            return $this->brandRepository->deleteBrand($id);
        } catch (\Exception $e) {
            Log::error('Failed to delete brand: ' . $e->getMessage());
            throw new \Exception('Failed to delete brand: ' . $e->getMessage());
        }
    }

    /**
     * Search brands by name with eloquent expression
     */
    public function searchBrandsByName(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->brandRepository->getAllBrands([
            'paginate' => true,
            'per_page' => $perPage,
            'search' => $search,
        ]);
    }

    /**
     * Get all brands for dropdown selection
     */
    public function fetchBrandsForDropdown(): array
    {
        return $this->brandRepository->getAllBrandsList();
    }

    /**
     * Validate brand data before processing
     */
    public function validateBrandData(array $data): array
    {
        $validated = [];

        if (isset($data['name'])) {
            $validated['name'] = trim($data['name']);
        }

        if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
            $validated['logo'] = $data['logo'];
        }

        return $validated;
    }

    /**
     * Fetch brands with statistics
     */
    public function fetchBrandsWithStatistics(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->brandRepository->getBrandsWithStats($params);
    }

    /**
     * Fetch brands with specific conditions
     */
    public function fetchBrandsWhere(array $conditions, array $params = []): Collection
    {
        return $this->brandRepository->getBrandsWhere($conditions, $params);
    }

    /**
     * Fetch brands by their IDs
     */
    public function fetchBrandsByIds(array $ids, array $params = []): Collection
    {
        return $this->brandRepository->getBrandsByIds($ids, $params);
    }

    /**
     * Fetch featured brands (custom logic example)
     */
    public function fetchFeaturedBrands(int $limit = 10): Collection
    {
        return $this->brandRepository->getAllBrands([
            'paginate' => false,
            'limit' => $limit,
            'order_by' => 'name',
            'order_direction' => 'asc',
        ]);
    }

    /**
     * Get paginated brands with custom ordering
     */
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
