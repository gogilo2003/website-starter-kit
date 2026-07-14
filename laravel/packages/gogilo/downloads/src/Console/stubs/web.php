<?php

use Illuminate\Support\Facades\Route;
use Gogilo\Downloads\Http\Controllers\DownloadController;

Route::middleware(['web', 'download.permission', 'download.throttle'])->group(function () {
    Route::get('/downloads/secure/{file}', [DownloadController::class, 'secureDownload'])
        ->name('downloads.secure');
    Route::get('/downloads/preview/{file}', [DownloadController::class, 'preview'])
        ->name('downloads.preview');
});
