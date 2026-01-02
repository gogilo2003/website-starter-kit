<?php

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

interface BrandRepositoryInterface
{
    /**
     * Get all brands with pagination
     */
    public function getAllBrands(array $params): LengthAwarePaginator|Collection|SupportCollection;

    /**
     * Get brand by ID
     */
    public function getBrandById(int $id);

    /**
     * Create a new brand
     */
    public function createBrand(array $data);

    /**
     * Update an existing brand
     */
    public function updateBrand(int $id, array $data);

    /**
     * Delete a brand
     */
    public function deleteBrand(int $id): bool;

    /**
     * Search brands by name
     */
    public function searchBrands(string $search, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all brands without pagination (for dropdowns, etc.)
     */
    public function getAllBrandsList(): array;
}
