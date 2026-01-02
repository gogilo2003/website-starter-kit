<?php

namespace App\Interfaces\Repositories;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface PictureRepositoryInterface
{
    public function all(array $params = []): array|Collection | SupportCollection;
    public function create(Model $model, array $data): null | Picture;
    public function update(Model $model, int $id, array $data): null|object;
    public function delete(int $id): bool;
    public function find(int $id): null|Picture;
    public function setPrimary(Model $model, int $id): bool;
}
