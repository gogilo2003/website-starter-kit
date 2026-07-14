<?php

use Gogilo\Downloads\Http\Controllers\DownloadCategoryController;
use Gogilo\Downloads\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::prefix('downloads')->name('-downloads')->group(function () {
        Route::get('{category_id?}/files', [DownloadController::class, 'index']);
        Route::post('', [DownloadController::class, 'store'])->name('-store');
        Route::patch('{download}', [DownloadController::class, 'update'])->name('-update');
        Route::delete('{download}', [DownloadController::class, 'destroy'])->name('-destroy');
        Route::patch('activate/{download}', [DownloadController::class, 'activate'])->name('-activate');
        Route::patch('feature/{download}', [DownloadController::class, 'feature'])->name('-feature');

        Route::prefix('categories')->name('-categories')->group(function () {
            Route::get('', [DownloadCategoryController::class, 'index']);
            Route::post('', [DownloadCategoryController::class, 'store'])->name('-store');
            Route::patch('{download_category}', [DownloadCategoryController::class, 'update'])->name('-update');
            Route::delete('{download_category}', [DownloadCategoryController::class, 'destroy'])->name('-destroy');
            Route::patch('activate/{download_category}', [DownloadCategoryController::class, 'activate'])->name('-activate');
        });
    });
});
