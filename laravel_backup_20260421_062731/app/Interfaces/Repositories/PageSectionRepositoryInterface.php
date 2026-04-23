<?php

namespace App\Interfaces\Repositories;

use App\Models\PageSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PageSectionRepositoryInterface
{
    public function all(int $perPage = 0, ?string $search = null, array $relations = [], bool $mapped = false);
    public function find(int $id): ?PageSection;
    public function create(array $data): PageSection;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function syncElements(int $pageSectionId, array $elementIds): array;
}
