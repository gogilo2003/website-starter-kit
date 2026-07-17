<?php

use Gogilo\News\Http\Controllers\NewsArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::prefix('news_articles')->name('-news_articles')->group(function () {
        Route::get('', [NewsArticleController::class, 'index']);
        Route::post('', [NewsArticleController::class, 'store'])->name('-store');
        Route::patch('{news_article}', [NewsArticleController::class, 'update'])->name('-update');
        Route::delete('{news_article}', [NewsArticleController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{news_article}', [NewsArticleController::class, 'publish'])->name('-publish');
        Route::patch('promote/{news_article}', [NewsArticleController::class, 'promote'])->name('-promote');
    });
});
