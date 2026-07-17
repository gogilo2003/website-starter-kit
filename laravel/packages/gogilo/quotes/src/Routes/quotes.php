<?php

use Gogilo\Quotes\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::prefix('quotes')->name('-quotes')->group(function () {
        Route::get('', [QuoteController::class, 'index']);
        Route::post('', [QuoteController::class, 'store'])->name('-store');
        Route::patch('{quote}', [QuoteController::class, 'update'])->name('-update');
        Route::delete('{quote}', [QuoteController::class, 'destroy'])->name('-destroy');
        Route::patch('status/{quote}', [QuoteController::class, 'updateStatus'])->name('-status');
        Route::patch('items/{item}', [QuoteController::class, 'updateQuoteItem'])->name('-items-update');
    });
});
