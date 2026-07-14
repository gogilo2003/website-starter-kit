<?php

namespace Gogilo\Products\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BrandRepositoryInterface
{
    public function getAllBrands(array $params): LengthAwarePaginator|Collection;

    public function getBrandById(int $id);

    public function createBrand(array $data);

    public function updateBrand(int $id, array $data);

    public function deleteBrand(int $id): bool;

    public function searchBrands(string $search, int $perPage = 15): LengthAwarePaginator;

    public function getAllBrandsList(): array;
}
