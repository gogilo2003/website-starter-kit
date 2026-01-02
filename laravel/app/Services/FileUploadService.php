<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public function handle(UploadedFile $uploadedFile, string $path = 'uploads')
    {
        $filename = time() . '_' . $uploadedFile->getClientOriginalName();
        $type = $uploadedFile->guessExtension();
        $size = $uploadedFile->getSize();

        $uploadedFile->storeAs($path, $filename);

        return [
            'path' => $path,
            'name' => $filename,
            'type' => $type,
            'size' => $size,
        ];
    }
}
