<?php

namespace Gogilo\Downloads\Repositories;

use Gogilo\Downloads\Models\DownloadCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class DownloadCategoryRepository implements DownloadCategoryRepositoryInterface
{
    public function all(array $params = []): array|Collection|SupportCollection|LengthAwarePaginator
    {
        $query = DownloadCategory::query();

        if (isset($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($params['relations']) && is_array($params['relations'])) {
            $query->with($params['relations']);
        }

        if (isset($params['paginate']) && $params['paginate']) {
            $perPage = $params['per_page'] ?? 10;

            return $query->paginate($perPage)
                ->through(fn (DownloadCategory $downloadCategory) => $this->mapCategory($downloadCategory));
        }

        return $query->get()
            ->map(fn (DownloadCategory $downloadCategory) => $this->mapCategory($downloadCategory));
    }

    public function find($id, array $relations = [], bool $mapped = false): ?DownloadCategory
    {
        return DownloadCategory::find($id);
    }

    public function findBySlug(string $slug, array $relations = [], bool $mapped = false): object|array|null
    {
        $query = DownloadCategory::where('slug', $slug);
        $query->with($relations);

        return $mapped ? $this->mapCategory($query->first()) : $query->first();
    }

    public function create(array $data): DownloadCategory|bool
    {
        try {
            $category = new DownloadCategory;
            $category->name = $data['name'];
            $category->slug = $data['slug'];
            $category->description = $data['description'] ?? null;
            $category->is_active = $data['is_active'] ?? true;
            $category->save();

            return $category;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update(DownloadCategory $category, array $data): DownloadCategory|bool
    {
        try {
            $category->name = $data['name'] ?? $category->name;
            $category->slug = $data['slug'] ?? $category->slug;
            $category->description = $data['description'] ?? $category->description;
            $category->is_active = $data['is_active'] ?? $category->is_active;
            $category->save();

            return $category;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(DownloadCategory $category): bool
    {
        try {
            $category->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function activate(DownloadCategory $category): bool
    {
        $category->is_active = ! $category->is_active;
        $category->save();

        return $category->is_active;
    }

    protected function mapCategory(DownloadCategory $downloadCategory)
    {
        $data = [
            'id' => $downloadCategory->id,
            'name' => $downloadCategory->name,
            'slug' => $downloadCategory->slug,
            'description' => $downloadCategory->description,
        ];

        if ($downloadCategory->relationLoaded('downloads')) {
            $data['downloads'] = $downloadCategory->downloads;
        }

        return (object) $data;
    }
}
