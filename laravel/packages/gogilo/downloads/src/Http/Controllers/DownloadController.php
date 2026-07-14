<?php

namespace Gogilo\Downloads\Http\Controllers;

use Gogilo\Downloads\DownloadManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DownloadController
{
    protected DownloadManager $downloadManager;

    public function __construct(DownloadManager $downloadManager)
    {
        $this->downloadManager = $downloadManager;
    }

    /**
     * Download a file via API.
     *
     * @param string $fileId
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function apiDownload(string $fileId, Request $request): BinaryFileResponse
    {
        return $this->downloadManager->download($fileId, $request);
    }

    /**
     * Get file metadata via API.
     *
     * @param string $fileId
     * @return JsonResponse
     */
    public function apiMetadata(string $fileId): JsonResponse
    {
        try {
            $metadata = $this->downloadManager->getMetadata($fileId);
            return response()->json($metadata);
        } catch (\Exception $e) {
            return response()->json(['error' => 'File not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get file preview URL via API.
     *
     * @param string $fileId
     * @return JsonResponse
     */
    public function apiPreview(string $fileId): JsonResponse
    {
        try {
            $url = $this->downloadManager->previewUrl($fileId);
            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'File not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Secure download with signed URL verification.
     *
     * @param string $fileId
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function secureDownload(string $fileId, Request $request): BinaryFileResponse
    {
        // Verify signature
        $signature = $request->route('signature');
        $expected = hash_hmac('sha256', $fileId, config('app.key'));
        
        if (!hash_equals($expected, $signature)) {
            abort(403, 'Invalid signature');
        }
        
        // Verify expiry
        $expires = $request->route('expires');
        if (time() > $expires) {
            abort(403, 'URL expired');
        }
        
        return $this->downloadManager->download($fileId, $request);
    }

    /**
     * Preview file in browser.
     *
     * @param string $fileId
     * @return BinaryFileResponse
     */
    public function preview(string $fileId): BinaryFileResponse
    {
        $disk = $this->downloadManager->getDisk(config('downloads.preview_disk'));
        $filePath = $this->downloadManager->getMetadata($fileId)['storage_path'];
        
        return $disk->response($filePath, $this->downloadManager->getMetadata($fileId)['original_filename']);
    }
}