<?php

namespace Gogilo\Downloads\Repositories;

use Gogilo\Downloads\Models\Download;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class DownloadRepository implements DownloadRepositoryInterface
{
    public function all(array $params = [], bool $mapped = false): array|Collection|SupportCollection|LengthAwarePaginator
    {
        $query = Download::query();

        if (isset($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhere('file_type', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($params['category_slug'])) {
            $query->whereHas('category', function ($query) use ($params) {
                $query->where('slug', $params['category_slug']);
            });
        }

        if (isset($params['category_id'])) {
            $query->where('download_category_id', $params['category_id']);
        }

        if (isset($params['relations']) && is_array($params['relations'])) {
            $query->with($params['relations']);
        }

        if (isset($params['paginate']) && $params['paginate']) {
            $perPage = $params['per_page'] ?? 10;

            return $mapped
                ? $query->paginate($perPage)->through(fn (Download $download) => $this->mapDownload($download))
                : $query->paginate($perPage);
        }

        return $mapped
            ? $query->get()->map(fn (Download $download) => $this->mapDownload($download))
            : $query->get();
    }

    public function find($id, bool $mapped = false): null|Download|array
    {
        return $mapped
            ? $this->mapDownload(Download::find($id))
            : Download::find($id);
    }

    public function create(array $data, bool $mapped = false): Download|array|bool
    {
        try {
            $download = new Download;
            $download->title = $data['title'];
            $download->slug = $data['slug'];
            $download->description = $data['description'] ?? null;
            $download->file_path = $data['file_path'];
            $download->file_name = $data['file_name'];
            $download->file_type = $data['file_type'] ?? null;
            $download->file_size = $data['file_size'] ?? null;
            $download->download_category_id = $data['category'] ?? null;
            $download->is_featured = $data['is_featured'] ?? false;
            $download->is_active = $data['is_active'] ?? true;
            $download->save();

            return $mapped ? $this->mapDownload($download) : $download;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update(Download $download, array $data, bool $mapped = false): Download|array|bool
    {
        try {
            $download->title = $data['title'] ?? $download->title;
            $download->slug = $data['slug'] ?? $download->slug;
            $download->description = $data['description'] ?? $download->description;
            $download->file_path = $data['file_path'] ?? $download->file_path;
            $download->file_name = $data['file_name'] ?? $download->file_name;
            $download->file_type = $data['file_type'] ?? $download->file_type;
            $download->file_size = $data['file_size'] ?? $download->file_size;
            $download->download_category_id = $data['category'] ?? $download->download_category_id;
            $download->is_featured = $data['is_featured'] ?? $download->is_featured;
            $download->is_active = $data['is_active'] ?? $download->is_active;
            $download->save();

            return $mapped ? $this->mapDownload($download) : $download;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(Download $download): bool
    {
        try {
            $download->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getByCategory($categoryId, bool $paginate = false, bool $mapped = false): array|Collection|SupportCollection|LengthAwarePaginator
    {
        $downloads = Download::where('download_category_id', $categoryId)->get();
        $downloads = $mapped
            ? $downloads->map(fn (Download $download) => $this->mapDownload($download))
            : $downloads;

        return $downloads;
    }

    public function activate(Download $download): bool
    {
        try {
            $download->is_active = ! $download->is_active;
            $download->save();

            return $download->is_active;
        } catch (\Exception $e) {
            throw new \Exception('Error toggling activation status: '.$e->getMessage());
        }
    }

    public function feature(Download $download): bool
    {
        try {
            $download->is_featured = ! $download->is_featured;
            $download->save();

            return $download->is_featured;
        } catch (\Exception $e) {
            throw new \Exception('Error toggling feature status: '.$e->getMessage());
        }
    }

    protected function mapDownload(Download $download)
    {
        return [
            'id' => $download->id,
            'title' => $download->title,
            'description' => $download->description,
            'name' => $download->file_name,
            'type' => $download->file_type,
            'category' => $download->category?->name,
            'size' => $download->file_size,
            'is_active' => $download->is_active,
            'is_featured' => $download->is_featured,
        ];
    }
}
