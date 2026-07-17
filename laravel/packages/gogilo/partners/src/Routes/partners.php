<?php

use Gogilo\Partners\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::prefix('partners')->name('-partners')->group(function () {
        Route::get('', [PartnerController::class, 'index']);
        Route::post('', [PartnerController::class, 'store'])->name('-store');
        Route::patch('{partner}', [PartnerController::class, 'update'])->name('-update');
        Route::delete('{partner}', [PartnerController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{partner}', [PartnerController::class, 'publish'])->name('-publish');
        Route::patch('promote/{partner}', [PartnerController::class, 'promote'])->name('-promote');
    });
});
