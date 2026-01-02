<?php

namespace App\Interfaces\Repositories;

use App\Models\Element;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

interface ElementRepositoryInterface
{
    public function all(int $perPage = 15, ?string $search = null): Collection|LengthAwarePaginator|SupportCollection;
    public function find(int $id): ?Element;
    public function findByName(string $name): ?Element;
    public function create(array $data): Element;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function syncPageSections(int $elementId, array $pageSectionIds): array;
}
