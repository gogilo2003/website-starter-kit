<?php

namespace Gogilo\PageSections\Interfaces\Repositories;

use Gogilo\PageSections\Models\PageSection;

interface PageSectionRepositoryInterface
{
    public function all(int $perPage = 0, ?string $search = null, array $relations = [], bool $mapped = false);
    public function find(int $id): ?PageSection;
    public function create(array $data): PageSection;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function syncElements(int $pageSectionId, array $elementIds): array;
}
