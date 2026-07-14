<?php

namespace Gogilo\Downloads\Services;

use Gogilo\Downloads\Models\Download;
use Gogilo\Downloads\Repositories\DownloadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class DownloadService
{
    protected DownloadRepositoryInterface $downloadRepository;

    public function __construct(DownloadRepositoryInterface $downloadRepository)
    {
        $this->downloadRepository = $downloadRepository;
    }

    public function all(array $params = [], bool $mapped = false): array|Collection|SupportCollection|LengthAwarePaginator
    {
        return $this->downloadRepository->all($params, $mapped);
    }

    public function find($id): ?Download
    {
        return $this->downloadRepository->find($id);
    }

    public function create(array $data): Download|bool
    {
        return $this->downloadRepository->create($data);
    }

    public function update($id, array $data): Download|bool
    {
        $download = $this->downloadRepository->find($id);

        if (! $download) {
            return false;
        }

        return $this->downloadRepository->update($download, $data);
    }

    public function delete($id): bool
    {
        $download = $this->downloadRepository->find($id);

        if (! $download) {
            return false;
        }

        return $this->downloadRepository->delete($download);
    }

    public function getByCategory($categoryId): array|Collection|SupportCollection|LengthAwarePaginator
    {
        return $this->downloadRepository->getByCategory($categoryId);
    }

    public function activate($id): bool
    {
        $download = $this->downloadRepository->find($id);

        if (! $download) {
            return false;
        }

        return $this->downloadRepository->activate($download);
    }

    public function feature($id): bool
    {
        $download = $this->downloadRepository->find($id);

        if (! $download) {
            return false;
        }

        return $this->downloadRepository->feature($download);
    }
}
