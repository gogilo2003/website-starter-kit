<?php

namespace App\Repositories;

use App\Models\Picture;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use App\Interfaces\Repositories\PictureRepositoryInterface;

class PictureRepository implements PictureRepositoryInterface
{
    public function all(array $params = []): array|Collection|SupportCollection
    {
        $query = Picture::query();

        if (isset($params['picturable_type']) && isset($params['picturable_id'])) {
            $query->where('picturable_type', $params['picturable_type'])
                ->where('picturable_id', $params['picturable_id']);
        }

        if (isset($params['is_primary'])) {
            $query->where('is_primary', $params['is_primary']);
        }

        return $query->get();
    }

    public function find(int $id): ?Picture
    {
        return Picture::find($id);
    }

    public function create(Model $model, array $data): ?Picture
    {
        $file = $data['picture'] ?? null;
        $directory = $data['path'] ?? 'pictures';

        if ($file instanceof UploadedFile && $file->isValid()) {
            $storedPath = $this->uploadPicture($file, $directory);

            $picture = new Picture();
            $picture->name = $storedPath;
            $picture->caption = $data['caption'] ?? null;
            $picture->is_primary = $data['is_primary'] ?? false;

            $model->pictures()->save($picture);

            return $picture;
        }

        return null;
    }

    public function update(Model $model, int $id, array $data): ?object
    {
        $picture = $this->find($id);

        if (!$picture) {
            return null;
        }

        $picture->caption = $data['caption'] ?? $picture->caption;
        $picture->is_primary = $data['is_primary'] ?? $picture->is_primary;

        if (isset($data['picture']) && $data['picture'] instanceof UploadedFile) {
            $directory = $data['path'] ?? 'pictures';
            $picture->name = $this->uploadPicture($data['picture'], $directory);
        }

        $picture->save();

        return $picture;
    }

    public function delete(int $id): bool
    {
        $picture = $this->find($id);

        if (!$picture) {
            return false;
        }

        // Delete physically from storage
        if ($picture->name && Storage::disk('public')->exists($picture->name)) {
            Storage::disk('public')->delete($picture->name);
        }

        return $picture->delete();
    }

    public function setPrimary(Model $model, int $id): bool
    {
        $picture = $this->find($id);

        if (
            !$picture || $picture->picturable_id !== $model->id ||
            $picture->picturable_type !== $model::class
        ) {
            return false;
        }

        // 1. Unset previous primary
        $model->pictures()->where('is_primary', true)->update(['is_primary' => false]);

        // 2. Set new primary
        $picture->is_primary = true;
        $picture->save();

        return true;
    }

    protected function uploadPicture(UploadedFile $file, string $directory = 'pictures'): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        return $file->store($directory, 'public');
    }
}
