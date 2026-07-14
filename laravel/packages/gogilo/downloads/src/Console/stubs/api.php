<?php

use Illuminate\Support\Facades\Route;
use Gogilo\Downloads\Http\Controllers\DownloadController;

Route::middleware(['api', 'download.throttle'])->group(function () {
    Route::get('/downloads/{file}', [DownloadController::class, 'apiDownload'])
        ->name('downloads.api.download');
    Route::get('/downloads/{file}/metadata', [DownloadController::class, 'apiMetadata'])
        ->name('downloads.api.metadata');
    Route::get('/downloads/{file}/preview', [DownloadController::class, 'apiPreview'])
        ->name('downloads.api.preview');
});
