<?php

namespace App\Services;

use App\Interfaces\Repositories\DownloadCategoryRepositoryInterface;
use App\Models\DownloadCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class DownloadCategoryService
{
    protected \App\Repositories\DownloadCategoryRepository $categoryRepository;

    public function __construct(DownloadCategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function all(array $params = []): array | Collection | SupportCollection | LengthAwarePaginator
    {
        return $this->categoryRepository->all($params);
    }

    public function getDownloadCategoryBySlug(string $slug, array $relations = [], bool $mapped = false)
    {
        return $this->categoryRepository->findBySlug($slug, $relations, $mapped);
    }

    public function find($id): ?DownloadCategory
    {
        return $this->categoryRepository->find($id);
    }

    public function create(array $data): DownloadCategory | bool
    {
        return $this->categoryRepository->create($data);
    }

    public function update($id, array $data): DownloadCategory | bool
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return false; // Category not found
        }

        return $this->categoryRepository->update($category, $data);
    }

    public function delete(int $id): bool
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return false; // Category not found
        }

        return $this->categoryRepository->delete($category);
    }

    public function activate(int $id): bool
    {
        $category = $this->categoryRepository->find($id);
        if (!$category) {
            return false; // Category not found
        }

        return $this->categoryRepository->activate($category);
    }
}
