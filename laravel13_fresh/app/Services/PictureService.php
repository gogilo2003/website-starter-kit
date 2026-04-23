<?php

namespace App\Services;

use App\Models\Picture;
use App\Repositories\PictureRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use App\Interfaces\Repositories\PictureRepositoryInterface;

class PictureService
{
    protected PictureRepository $pictureRepository;

    /**
     * Inject the Picture Repository.
     */
    public function __construct(PictureRepositoryInterface $pictureRepository)
    {
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * Fetch all pictures with optional filters.
     */
    public function getAllPictures(array $filters = []): array|Collection|SupportCollection
    {
        return $this->pictureRepository->all($filters);
    }

    /**
     * Retrieve a single picture by its ID.
     */
    public function getPictureById(int $pictureId): ?Picture
    {
        return $this->pictureRepository->find($pictureId);
    }

    /**
     * Store a new picture attached to a model.
     */
    public function createPicture(Model $model, array $data): ?Picture
    {
        return $this->pictureRepository->create($model, $data);
    }

    /**
     * Update an existing picture record.
     */
    public function updatePicture(Model $model, int $pictureId, array $data): ?Picture
    {
        return $this->pictureRepository->update($model, $pictureId, $data);
    }

    /**
     * Delete a picture and remove the stored file if necessary.
     */
    public function deletePicture(int $pictureId): bool
    {
        return $this->pictureRepository->delete($pictureId);
    }

    /**
     * Set a picture as the primary image for a picturable model.
     */
    public function setPrimaryPicture(Model $model, int $pictureId): bool
    {
        return $this->pictureRepository->setPrimary($model, $pictureId);
    }

    /**
     * Replace a picture entirely (delete old, upload new).
     */
    public function replacePicture(Model $model, int $pictureId, array $data): ?Picture
    {
        // deletes the old file & updates DB
        return $this->pictureRepository->update($model, $pictureId, $data);
    }

    /**
     * Upload a picture without attaching to a model.
     * Useful for temporary uploads, preview uploads, etc.
     */
    public function uploadStandalonePicture(array $data): ?string
    {
        $directory = $data['path'] ?? 'pictures';
        $file = $data['picture'];

        if (!$file->isValid()) {
            return null;
        }

        return $file->store($directory, 'public');
    }
}
