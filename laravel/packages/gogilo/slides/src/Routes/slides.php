<?php

use Gogilo\Slides\Http\Controllers\SlideController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::prefix('slides')->name('-slides')->group(function () {
        Route::get('', [SlideController::class, 'index']);
        Route::post('', [SlideController::class, 'store'])->name('-store');
        Route::patch('{slide}', [SlideController::class, 'update'])->name('-update');
        Route::delete('{slide}', [SlideController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{slide}', [SlideController::class, 'publish'])->name('-publish');
    });
});
