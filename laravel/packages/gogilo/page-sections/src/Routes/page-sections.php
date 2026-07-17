<?php

use Gogilo\PageSections\Http\Controllers\ElementController;
use Gogilo\PageSections\Http\Controllers\PageSectionController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    
    Route::prefix('page_sections')->name('-page-sections')->group(function () {
        Route::get('', [PageSectionController::class, 'index']);
        Route::post('', [PageSectionController::class, 'store'])->name('-store');
        Route::patch('{page_section}', [PageSectionController::class, 'update'])->name('-update');
        Route::delete('{page_section}', [PageSectionController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{page_section}', [PageSectionController::class, 'publish'])->name('-publish');
        Route::patch('sync-elements/{page_section}', [PageSectionController::class, 'syncElements'])->name('-sync-elements');
    });

    Route::prefix('elements')->name('-elements')->group(function () {
        Route::get('', [ElementController::class, 'index']);
        Route::post('', [ElementController::class, 'store'])->name('-store');
        Route::patch('{element}', [ElementController::class, 'update'])->name('-update');
        Route::delete('{element}', [ElementController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{element}', [ElementController::class, 'publish'])->name('-publish');
    });
});
