<?php

namespace Gogilo\Downloads;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class DownloadManager
{
    protected FilesystemFactory $filesystem;
    protected array $config;

    public function __construct(FilesystemFactory $filesystem, array $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
    }

    /**
     * Validate and process a download request.
     *
     * @param string $fileId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function download(string $fileId, Request $request)
    {
        $download = $this->getDownloadModel()::findOrFail($fileId);

        // Verify permissions if custom permission middleware is configured
        if ($this->config['verify_permissions'] && ! $this->checkPermission($download, $request)) {
            abort(403, 'Unauthorized download access');
        }

        $diskName = $download->disk;
        $disk = $this->filesystem->disk($diskName);
        $filePath = $download->storage_path;

        if (! $disk->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Increment download count
        $download->increment('download_count');

        return $disk->download($filePath, $download->original_filename, [
            'Content-Type' => $download->mime_type,
            'Content-Length' => $download->file_size,
        ]);
    }

    /**
     * Generate a signed URL for secure downloads.
     *
     * @param int|string $fileId
     * @param array $options
     * @return string
     */
    public function signedUrl($fileId, array $options = []): string
    {
        $expires = $options['expires'] ?? now()->addMinutes($this->config['signed_url_expiry_minutes']);
        
        return Route::signedRoute('downloads.secure', [
            'file' => $fileId,
            'expires' => $expires->getTimestamp(),
        ]);
    }

    /**
     * Store a new file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $disk
     * @param array $metadata
     * @return Model
     */
    public function store($file, ?string $disk = null, array $metadata = [])
    {
        $diskName = $disk ?? $this->config['default_disk'];
        $path = $file->store('downloads', $diskName);

        return $this->getDownloadModel()::create([
            'original_filename' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'disk' => $diskName,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get download model instance.
     *
     * @return string
     */
    protected function getDownloadModel(): string
    {
        return $this->config['model_class'];
    }

    /**
     * Check download permissions.
     *
     * @param Model $download
     * @param Request $request
     * @return bool
     */
    protected function checkPermission(Model $download, Request $request): bool
    {
        // Implement custom permission logic
        return Auth::check();
    }

    /**
     * Get filesystem disk.
     *
     * @param string|null $disk
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getFilesystem(?string $disk): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return $this->filesystem->disk($disk ?? $this->config['default_disk']);
    }

    /**
     * Get filesystem disk by name.
     *
     * @param string|null $disk
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getDisk(?string $disk = null): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return $this->getFilesystem($disk);
    }

    /**
     * Get file preview URL.
     *
     * @param int|string $fileId
     * @return string
     */
    public function previewUrl($fileId): string
    {
        return route('downloads.preview', ['file' => $fileId]);
    }

    /**
     * Get file metadata.
     *
     * @param int|string $fileId
     * @return array
     */
    public function getMetadata($fileId): array
    {
        $download = $this->getDownloadModel()::findOrFail($fileId);
        
        return [
            'id' => $download->id,
            'original_filename' => $download->original_filename,
            'file_size' => $download->file_size,
            'mime_type' => $download->mime_type,
            'disk' => $download->disk,
            'storage_path' => $download->storage_path,
            'download_count' => $download->download_count,
            'created_at' => $download->created_at,
        ];
    }
}