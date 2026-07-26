<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/run-migrations/{key}', function ($key) {
    if (! Hash::check(
        $key,
        '$2y$12$TJeyinZ7.dsHRh9QghNZMe8uDJpVF9wjAY2qpV3NTi8H4Q7Q4.07i'
    )) {
        return 'Invalid key';
    }

    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();

    // Remove this route after successful execution
    $routeFilePath = base_path('routes/web.php');
    $routeFileContent = file_get_contents($routeFilePath);
    $routeFileContent = preg_replace('/Route::get$$\'\/run-migrations\/.*?\}$$;/s', '', $routeFileContent);
    file_put_contents($routeFilePath, $routeFileContent);

    return 'Migrations completed. Output: '.nl2br($output);
});

Route::controller(WebController::class)
    ->group(function () {
        Route::get('/', 'home')->name('home');
        Route::get('/about', 'about')->name('about');
        Route::get('/products/{slug?}', 'products')->name('products');
        Route::get('/product/{product_slug}', 'product')->name('product');
        Route::get('/services', 'services')->name('services');
        Route::get('/downloads/{slug?}', 'downloads')->name('downloads');
        Route::get('/download/{slug}', 'download')->name('download');
        Route::get('/contact', 'contact')->name('contact');
        Route::get('/quote', 'quote')->name('quote');
        Route::get('/quote/track/{code}', 'quoteTrack')->name('quote-track');
        Route::get('/quote/download/{code}', 'quoteDownload')->name('quote-download');
        Route::post('/quote/request', 'quoteRequest')->name('quote-request');
        Route::get('/news/{slug?}', 'news')->name('news');
        Route::post('/wishlist/add', 'wishlistAdd')->name('wishlist-add');
        Route::delete('/wishlist/remove', 'wishlistRemove')->name('wishlist-remove');
    });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->name('dashboard')->group(function () {
    Route::get('', [DashboardController::class, 'dashboard']);

    Route::prefix('migrations')->name('-migrations')->group(function () {
        Route::get('/', [MigrationController::class, 'index']);
        Route::post('/run', [MigrationController::class, 'runMigrations'])->name('-run');
        Route::post('/execute', [MigrationController::class, 'execute'])->name('-execute');
        Route::post('/rollback', [MigrationController::class, 'rollbackMigrations'])->name('-rollback');
    });








});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/test-page', [\App\Http\Controllers\TestPageController::class, '__invoke'])->name('test-page');
