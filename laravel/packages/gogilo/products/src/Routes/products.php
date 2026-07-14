<?php

use Gogilo\Products\Http\Controllers\BrandController;
use Gogilo\Products\Http\Controllers\ProductCategoryController;
use Gogilo\Products\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::prefix('products')->name('-products')->group(function () {
        Route::get('{category}/list', [ProductController::class, 'index']);
        Route::post('', [ProductController::class, 'store'])->name('-store');
        Route::patch('{product}', [ProductController::class, 'update'])->name('-update');
        Route::delete('{product}', [ProductController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{product}', [ProductController::class, 'publish'])->name('-publish');
        Route::patch('promote/{product}', [ProductController::class, 'promote'])->name('-promote');

        Route::prefix('categories')->name('-categories')->group(function () {
            Route::get('', [ProductCategoryController::class, 'index']);
            Route::post('', [ProductCategoryController::class, 'store'])->name('-store');
            Route::patch('{category}', [ProductCategoryController::class, 'update'])->name('-update');
            Route::delete('{category}', [ProductCategoryController::class, 'destroy'])->name('-destroy');
            Route::patch('publish/{category}', [ProductCategoryController::class, 'publish'])->name('-publish');
            Route::patch('promote/{category}', [ProductCategoryController::class, 'promote'])->name('-promote');
        });
    });

    Route::prefix('brands')->name('-brands')->group(function () {
        Route::get('', [BrandController::class, 'index']);
        Route::post('', [BrandController::class, 'store'])->name('-store');
        Route::patch('{brand}', [BrandController::class, 'update'])->name('-update');
        Route::delete('{brand}', [BrandController::class, 'destroy'])->name('-destroy');
    });
});
