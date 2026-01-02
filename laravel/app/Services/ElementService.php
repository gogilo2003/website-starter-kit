<?php

namespace App\Services;

use App\Models\Element;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use App\Interfaces\Repositories\ElementRepositoryInterface;

class ElementService
{
    protected \App\Repositories\ElementRepository $elementRepository;

    public function __construct(ElementRepositoryInterface $elementRepository)
    {
        $this->elementRepository = $elementRepository;
    }

    public function getAllElements(int $perPage = 15, ?string $search = null): Collection|SupportCollection|LengthAwarePaginator
    {
        return $this->elementRepository->all($perPage, $search);
    }

    public function getElement(int $id): ?Element
    {
        return $this->elementRepository->find($id);
    }

    public function createElement(array $data): Element
    {
        return $this->elementRepository->create($data);
    }

    public function updateElement(int $id, array $data): bool
    {
        return $this->elementRepository->update($id, $data);
    }

    public function deleteElement(int $id): bool
    {
        return $this->elementRepository->delete($id);
    }

    public function syncElementPageSections(int $elementId, array $pageSectionIds): array
    {
        return $this->elementRepository->syncPageSections($elementId, $pageSectionIds);
    }
}
