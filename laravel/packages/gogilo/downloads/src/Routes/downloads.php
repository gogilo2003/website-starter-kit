<?php

use Illuminate\Support\Facades\Route;
use Gogilo\Downloads\Http\Controllers\DownloadController;
use Gogilo\Downloads\Http\Middleware\DownloadPermissionMiddleware;
use Gogilo\Downloads\Http\Middleware\DownloadThrottleMiddleware;

// API Routes
Route::middleware(['api', 'download.throttle'])->prefix('api')->group(function () {
    Route::get('/downloads/{file}', [DownloadController::class, 'apiDownload'])
        ->name('downloads.api.download');
    Route::get('/downloads/{file}/metadata', [DownloadController::class, 'apiMetadata'])
        ->name('downloads.api.metadata');
    Route::get('/downloads/{file}/preview', [DownloadController::class, 'apiPreview'])
        ->name('downloads.api.preview');
});

// Web Routes
Route::middleware(['web', 'download.permission', 'download.throttle'])->group(function () {
    Route::get('/downloads/secure/{file}', [DownloadController::class, 'secureDownload'])
        ->name('downloads.secure');
    Route::get('/downloads/preview/{file}', [DownloadController::class, 'preview'])
        ->name('downloads.preview');
});