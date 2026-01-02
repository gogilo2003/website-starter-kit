<?php

namespace App\Repositories;

use App\Interfaces\Repositories\BrandRepositoryInterface;
use App\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * Get all brands with flexible parameters
     *
     * @param array $params [
     *     'paginate' => bool,        // Whether to paginate results
     *     'per_page' => int,         // Items per page (if paginate is true)
     *     'search' => string,        // Search term for brand name
     *     'relations' => array,      // Eager load relations
     *     'columns' => array,        // Specific columns to select
     *     'order_by' => string,      // Column to order by
     *     'order_direction' => string, // asc or desc
     *     'with_trashed' => bool,    // Include soft deleted records
     *     'only_trashed' => bool,    // Only trashed records
     *     'limit' => int,            // Limit number of results
     *     'where' => array,          // Additional where conditions
     *     'where_in' => array,       // Where in conditions
     * ]
     */
    public function getAllBrands(array $params = []): LengthAwarePaginator|Collection
    {
        // Set default parameters
        $defaultParams = [
            'paginate' => true,
            'per_page' => 15,
            'search' => null,
            'relations' => [],
            'columns' => ['*'],
            'order_by' => 'created_at',
            'order_direction' => 'desc',
            'with_trashed' => false,
            'only_trashed' => false,
            'limit' => null,
            'where' => [],
            'where_in' => [],
        ];

        // Merge default parameters with provided ones
        $params = array_merge($defaultParams, $params);

        try {
            // Start building the query
            $query = Brand::query();

            // Handle search functionality
            if (!empty($params['search'])) {
                $searchTerm = $params['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('id', '=', $searchTerm);
                });
            }

            // Handle eager loading of relations
            if (!empty($params['relations'])) {
                $query->with($params['relations']);
            }

            // Handle additional where conditions
            if (!empty($params['where'])) {
                foreach ($params['where'] as $condition) {
                    $query->where($condition['column'], $condition['operator'], $condition['value']);
                }
            }

            // Handle whereIn conditions
            if (!empty($params['where_in'])) {
                foreach ($params['where_in'] as $condition) {
                    $query->whereIn($condition['column'], $condition['values']);
                }
            }

            // Handle trashed records
            if ($params['with_trashed']) {
                $query->withTrashed();
            } elseif ($params['only_trashed']) {
                $query->onlyTrashed();
            }

            // Apply ordering
            if (!empty($params['order_by'])) {
                $orderDirection = in_array(strtolower($params['order_direction']), ['asc', 'desc'])
                    ? $params['order_direction']
                    : 'desc';
                $query->orderBy($params['order_by'], $orderDirection);
            }

            // Apply limit if specified
            if (!empty($params['limit'])) {
                $query->limit($params['limit']);
            }

            // Select specific columns
            $query->select($params['columns']);

            // Return results based on pagination parameter
            if ($params['paginate']) {
                return $query->paginate($params['per_page']);
            } else {
                // Return collection (all results)
                $results = $query->get();

                // If we need to support SupportCollection specifically
                if ($results instanceof \Illuminate\Database\Eloquent\Collection) {
                    return $results;
                }

                return new Collection($results);
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch brands: ' . $e->getMessage());
            throw new \Exception('Failed to fetch brands: ' . $e->getMessage());
        }
    }

    /**
     * Get brand by ID with optional relations
     */
    public function getBrandById(int $id, array $relations = [])
    {
        try {
            $query = Brand::query();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->findOrFail($id);
        } catch (\Exception $e) {
            Log::error("Failed to fetch brand with ID {$id}: " . $e->getMessage());
            throw new \Exception("Brand not found or error occurred");
        }
    }

    public function createBrand(array $data)
    {
        try {
            if (isset($data['logo'])) {
                $data['logo'] = $this->storeLogo($data['logo']);
            }

            return Brand::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create brand: ' . $e->getMessage());
            throw new \Exception('Failed to create brand: ' . $e->getMessage());
        }
    }

    public function updateBrand(int $id, array $data)
    {
        try {
            $brand = $this->getBrandById($id);

            if (isset($data['logo'])) {
                // Delete old logo if exists
                if ($brand->logo) {
                    Storage::delete('public/' . $brand->logo);
                }
                $data['logo'] = $this->storeLogo($data['logo']);
            }

            $brand->update($data);

            return $brand;
        } catch (\Exception $e) {
            Log::error("Failed to update brand with ID {$id}: " . $e->getMessage());
            throw new \Exception('Failed to update brand: ' . $e->getMessage());
        }
    }

    public function deleteBrand(int $id): bool
    {
        try {
            $brand = $this->getBrandById($id);

            // Delete logo file if exists
            if ($brand->logo) {
                Storage::delete('public/' . $brand->logo);
            }

            return $brand->delete();
        } catch (\Exception $e) {
            Log::error("Failed to delete brand with ID {$id}: " . $e->getMessage());
            throw new \Exception('Failed to delete brand: ' . $e->getMessage());
        }
    }

    /**
     * Search brands by name (kept for backward compatibility)
     */
    public function searchBrands(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->getAllBrands([
            'paginate' => true,
            'per_page' => $perPage,
            'search' => $search,
        ]);
    }

    /**
     * Get all brands without pagination (for dropdowns, etc.)
     */
    public function getAllBrandsList(): array
    {
        try {
            return $this->getAllBrands([
                'paginate' => false,
                'columns' => ['id', 'name', 'logo'],
                'order_by' => 'name',
                'order_direction' => 'asc',
            ])->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to fetch brands list: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Store logo file and return the path
     */
    private function storeLogo($logoFile): string
    {
        try {
            $path = $logoFile->store('brands', 'public');

            // Remove 'public/' prefix as Laravel stores files with that in the path
            return str_replace('public/', '', $path);
        } catch (\Exception $e) {
            Log::error('Failed to store brand logo: ' . $e->getMessage());
            throw new \Exception('Failed to upload logo: ' . $e->getMessage());
        }
    }

    /**
     * Get brands with pagination and statistics
     */
    public function getBrandsWithStats(array $params = []): LengthAwarePaginator|Collection
    {
        $params['relations'] = ['products'];
        $brands = $this->getAllBrands($params);

        // Add product count to each brand
        if ($brands instanceof LengthAwarePaginator || $brands instanceof Collection) {
            $brands->each(function ($brand) {
                $brand->products_count = $brand->products->count();
            });
        }

        return $brands;
    }

    /**
     * Get brands with specific conditions
     */
    public function getBrandsWhere(array $conditions, array $params = []): Collection
    {
        $params['paginate'] = false;
        $params['where'] = $conditions;

        return $this->getAllBrands($params);
    }

    /**
     * Get brands by IDs
     */
    public function getBrandsByIds(array $ids, array $params = []): Collection
    {
        $params['paginate'] = false;
        $params['where_in'] = [['column' => 'id', 'values' => $ids]];

        return $this->getAllBrands($params);
    }
}
