<?php

namespace App\Services;

use App\Models\PageSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Interfaces\Repositories\PageSectionRepositoryInterface;

class PageSectionService
{
    protected \App\Repositories\PageSectionRepository $pageSectionRepository;

    public function __construct(PageSectionRepositoryInterface $pageSectionRepository)
    {
        $this->pageSectionRepository = $pageSectionRepository;
    }

    public function getAllPageSections(int $perPage = 0, ?string $search = null): Collection|LengthAwarePaginator
    {
        return $this->pageSectionRepository->all($perPage, $search);
    }

    public function getPageSection(int $id): ?PageSection
    {
        return $this->pageSectionRepository->find($id);
    }

    public function getByPageSectionName(string $name, array $relations = [], $mapped = false)
    {
        if ($allSections = $this->pageSectionRepository->all(0, $name, $relations, $mapped)) {
            return $allSections->first();
        }

        return null;
    }

    public function createPageSection(array $data): PageSection
    {
        return $this->pageSectionRepository->create($data);
    }

    public function updatePageSection(int $id, array $data): bool
    {
        return $this->pageSectionRepository->update($id, $data);
    }

    public function deletePageSection(int $id): bool
    {
        return $this->pageSectionRepository->delete($id);
    }

    public function syncElementsWithPageSection(int $pageSectionId, array $elementIds): array
    {
        return $this->pageSectionRepository->syncElements($pageSectionId, $elementIds);
    }
}
