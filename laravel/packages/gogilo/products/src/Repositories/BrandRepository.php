<?php

namespace Gogilo\Products\Repositories;

use Gogilo\Products\Models\Brand;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandRepository implements BrandRepositoryInterface
{
    public function getAllBrands(array $params = []): LengthAwarePaginator|Collection
    {
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

        $params = array_merge($defaultParams, $params);

        try {
            $query = Brand::query();

            if (! empty($params['search'])) {
                $searchTerm = $params['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('id', '=', $searchTerm);
                });
            }

            if (! empty($params['relations'])) {
                $query->with($params['relations']);
            }

            if (! empty($params['where'])) {
                foreach ($params['where'] as $condition) {
                    $query->where($condition['column'], $condition['operator'], $condition['value']);
                }
            }

            if (! empty($params['where_in'])) {
                foreach ($params['where_in'] as $condition) {
                    $query->whereIn($condition['column'], $condition['values']);
                }
            }

            if ($params['with_trashed']) {
                $query->withTrashed();
            } elseif ($params['only_trashed']) {
                $query->onlyTrashed();
            }

            if (! empty($params['order_by'])) {
                $orderDirection = in_array(strtolower($params['order_direction']), ['asc', 'desc'])
                    ? $params['order_direction']
                    : 'desc';
                $query->orderBy($params['order_by'], $orderDirection);
            }

            if (! empty($params['limit'])) {
                $query->limit($params['limit']);
            }

            $query->select($params['columns']);

            if ($params['paginate']) {
                return $query->paginate($params['per_page']);
            }

            $results = $query->get();

            if ($results instanceof \Illuminate\Database\Eloquent\Collection) {
                return $results;
            }

            return new Collection($results);
        } catch (\Exception $e) {
            Log::error('Failed to fetch brands: '.$e->getMessage());
            throw new \Exception('Failed to fetch brands: '.$e->getMessage());
        }
    }

    public function getBrandById(int $id, array $relations = [])
    {
        try {
            $query = Brand::query();

            if (! empty($relations)) {
                $query->with($relations);
            }

            return $query->findOrFail($id);
        } catch (\Exception $e) {
            Log::error("Failed to fetch brand with ID {$id}: ".$e->getMessage());
            throw new \Exception('Brand not found or error occurred');
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
            Log::error('Failed to create brand: '.$e->getMessage());
            throw new \Exception('Failed to create brand: '.$e->getMessage());
        }
    }

    public function updateBrand(int $id, array $data)
    {
        try {
            $brand = $this->getBrandById($id);

            if (isset($data['logo'])) {
                if ($brand->logo) {
                    Storage::delete('public/'.$brand->logo);
                }
                $data['logo'] = $this->storeLogo($data['logo']);
            }

            $brand->update($data);

            return $brand;
        } catch (\Exception $e) {
            Log::error("Failed to update brand with ID {$id}: ".$e->getMessage());
            throw new \Exception('Failed to update brand: '.$e->getMessage());
        }
    }

    public function deleteBrand(int $id): bool
    {
        try {
            $brand = $this->getBrandById($id);

            if ($brand->logo) {
                Storage::delete('public/'.$brand->logo);
            }

            return $brand->delete();
        } catch (\Exception $e) {
            Log::error("Failed to delete brand with ID {$id}: ".$e->getMessage());
            throw new \Exception('Failed to delete brand: '.$e->getMessage());
        }
    }

    public function searchBrands(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->getAllBrands([
            'paginate' => true,
            'per_page' => $perPage,
            'search' => $search,
        ]);
    }

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
            Log::error('Failed to fetch brands list: '.$e->getMessage());

            return [];
        }
    }

    private function storeLogo($logoFile): string
    {
        try {
            $path = $logoFile->store('brands', 'public');

            return str_replace('public/', '', $path);
        } catch (\Exception $e) {
            Log::error('Failed to store brand logo: '.$e->getMessage());
            throw new \Exception('Failed to upload logo: '.$e->getMessage());
        }
    }

    public function getBrandsWithStats(array $params = []): LengthAwarePaginator|Collection
    {
        $params['relations'] = ['products'];
        $brands = $this->getAllBrands($params);

        if ($brands instanceof LengthAwarePaginator || $brands instanceof Collection) {
            $brands->each(function ($brand) {
                $brand->products_count = $brand->products->count();
            });
        }

        return $brands;
    }

    public function getBrandsWhere(array $conditions, array $params = []): Collection
    {
        $params['paginate'] = false;
        $params['where'] = $conditions;

        return $this->getAllBrands($params);
    }

    public function getBrandsByIds(array $ids, array $params = []): Collection
    {
        $params['paginate'] = false;
        $params['where_in'] = [['column' => 'id', 'values' => $ids]];

        return $this->getAllBrands($params);
    }
}
