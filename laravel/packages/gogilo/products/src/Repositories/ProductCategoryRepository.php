<?php

namespace Gogilo\Products\Repositories;

use App\Support\Util;
use Gogilo\Products\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class ProductCategoryRepository implements ProductCategoryRepositoryInterface
{
    public function all(array $params = [], bool $mapped = false): LengthAwarePaginator|Collection
    {
        $query = ProductCategory::query();

        if (! empty($params['filters'])) {
            foreach ($params['filters'] as $field => $value) {
                $query->where($field, 'like', "%{$value}%");
            }
        }

        if (! empty($params['sort_by'])) {
            $query->orderBy($params['sort_by'], $params['sort_dir'] ?? 'asc');
        }

        if (! empty($params['relations'])) {
            $query->with($params['relations']);
        }

        if (! empty($params['paginate']) || ! empty($params['per_page'])) {
            $perPage = $params['per_page'] ?? 15;
            $categories = $mapped
                ? $query->paginate($perPage)
                    ->through(fn (ProductCategory $category) => $this->mapCategory($category))
                : $query->paginate($perPage);
        } else {
            $categories = $mapped
                ? $query->get()
                    ->map(fn (ProductCategory $category) => $this->mapCategory($category))
                : $query->get();
        }

        return $categories;
    }

    public function getById(int $id, bool $mapped = false, array $relations = []): ?ProductCategory
    {
        $query = ProductCategory::query()->with($relations);
        $category = $query->find($id);

        return $mapped && $category ? $this->mapCategory($category) : $category;
    }

    public function getBySlug(string $slug, bool $mapped = false, array $relations = []): ?ProductCategory
    {
        $query = ProductCategory::query()->with($relations);
        $category = $query->where('slug', $slug)->first();

        return $mapped && $category ? $this->mapCategory($category) : $category;
    }

    public function create(array $data): ProductCategory
    {
        $category = new ProductCategory;
        $category->slug = $data['slug'];
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $category->icon = $data['icon'] ?? null;

        $filename = null;
        $picture = $data['picture'] ?? null;
        if ($picture && $picture->isValid()) {
            $filename = $picture->store('products/categories', 'public');
        }
        $category->picture = $filename;
        $category->published = $data['published'] ?? false;
        $category->promoted = $data['promoted'] ?? false;

        $category->save();

        return $category;
    }

    public function update(int $id, array $data): ProductCategory
    {
        $category = ProductCategory::findOrFail($id);

        $category->slug = $data['slug'] ?? $category->slug;
        $category->name = $data['name'] ?? $category->name;
        $category->description = $data['description'] ?? $category->description;
        $category->icon = $data['icon'] ?? $category->icon;

        $picture = $data['picture'] ?? null;
        if ($picture && $picture->isValid()) {
            $filename = $picture->store('products/categories', 'public');
            $category->picture = $filename;
        }
        $category->published = $data['published'] ?? $category->published;
        $category->promoted = $data['promoted'] ?? $category->promoted;

        $category->save();

        return $category;
    }

    public function delete(int $id): bool
    {
        $category = ProductCategory::findOrFail($id);

        if ($category->picture && Storage::exists($category->picture)) {
            Storage::delete($category->picture);
        }

        return $category->delete();
    }

    public function publish(int $id): bool
    {
        $category = ProductCategory::findOrFail($id);
        $category->published = $category->published ? false : true;
        $category->save();

        return $category->published;
    }

    public function promote(int $id): bool
    {
        $category = ProductCategory::findOrFail($id);
        $category->promoted = $category->promoted ? false : true;
        $category->save();

        return $category->promoted;
    }

    protected function mapCategory(ProductCategory $category): ProductCategory
    {
        $category->picture_url = $category->picture ? Util::pictureUrl($category->picture) : url('/images/placeholder-product.png');

        return $category;
    }
}
