<?php

namespace App\Interfaces\Repositories;

use App\Models\Download;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface DownloadRepositoryInterface
{
    public function all(array $params = [], bool $mapped = false): array | Collection | SupportCollection | LengthAwarePaginator;
    public function find($id, bool $mapped = false): null| Download|array;
    public function create(array $data, bool $mapped = false): Download | array|bool;
    public function update(Download $download, array $data, bool $mapped = false): Download | array | bool;
    public function delete(Download $download): bool;
    public function getByCategory($categoryId, bool $paginate = true, bool $mapped = false): array | Collection | SupportCollection | LengthAwarePaginator;
    public function activate(Download $download): bool;
    public function feature(Download $download): bool;
}
