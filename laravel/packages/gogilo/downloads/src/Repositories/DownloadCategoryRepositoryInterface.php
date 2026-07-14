<?php

namespace Gogilo\Downloads\Repositories;

use Gogilo\Downloads\Models\DownloadCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface DownloadCategoryRepositoryInterface
{
    public function all(array $params): array|Collection|SupportCollection|LengthAwarePaginator;

    public function find($id): ?DownloadCategory;

    public function findBySlug(string $slug, array $relations = [], bool $mapped = false): object|array|null;

    public function create(array $data): DownloadCategory|bool;

    public function update(DownloadCategory $category, array $data): DownloadCategory|bool;

    public function delete(DownloadCategory $category): bool;

    public function activate(DownloadCategory $category): bool;
}
