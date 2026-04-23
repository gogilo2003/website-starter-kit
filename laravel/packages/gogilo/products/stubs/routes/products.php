<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard/products')->name('dashboard-products')->group(function () {
    Route::get('{category}/list', [ProductController::class, 'index'])->name('-index');
    Route::post('', [ProductController::class, 'store'])->name('-store');
    Route::patch('{product}', [ProductController::class, 'update'])->name('-update');
    Route::delete('{product}', [ProductController::class, 'destroy'])->name('-destroy');
    Route::patch('publish/{product}', [ProductController::class, 'publish'])->name('-publish');
    Route::patch('promote/{product}', [ProductController::class, 'promote'])->name('-promote');
});
