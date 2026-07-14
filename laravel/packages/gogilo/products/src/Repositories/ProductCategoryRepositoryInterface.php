<?php

namespace Gogilo\Products\Repositories;

use Gogilo\Products\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductCategoryRepositoryInterface
{
    public function all(array $params = [], bool $mapped = false): LengthAwarePaginator|Collection;

    public function getById(int $id, bool $mapped = false, array $relations = []): ?ProductCategory;

    public function getBySlug(string $slug, bool $mapped = false, array $relations = []): ?ProductCategory;

    public function create(array $data): ProductCategory;

    public function update(int $id, array $data): ProductCategory;

    public function delete(int $id): bool;

    public function publish(int $id): bool;

    public function promote(int $id): bool;
}
