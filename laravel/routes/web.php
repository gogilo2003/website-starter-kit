<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\WebController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\NewsArticleController;
use App\Http\Controllers\PageSectionController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\DownloadCategoryController;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/run-migrations/{key}', function ($key) {
    if (!\Illuminate\Support\Facades\Hash::check(
        $key,
        '$2y$12$TJeyinZ7.dsHRh9QghNZMe8uDJpVF9wjAY2qpV3NTi8H4Q7Q4.07i'
    )) {
        return "Invalid key";
    }

    \Illuminate\Support\Facades\Artisan::call('migrate', ["--force" => true]);
    $output = \Illuminate\Support\Facades\Artisan::output();

    // Remove this route after successful execution
    $routeFilePath = base_path('routes/web.php');
    $routeFileContent = file_get_contents($routeFilePath);
    $routeFileContent = preg_replace('/Route::get$$\'\/run-migrations\/.*?\}$$;/s', '', $routeFileContent);
    file_put_contents($routeFilePath, $routeFileContent);

    return 'Migrations completed. Output: ' . nl2br($output);
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
        Route::get('/quote',  'quote')->name('quote');
        Route::get('/quote/track/{code}',  'quoteTrack')->name('quote-track');
        Route::get('/quote/download/{code}',  'quoteDownload')->name('quote-download');
        Route::post('/quote/request',  'quoteRequest')->name('quote-request');
        Route::post('/wishlist/add',  'wishlistAdd')->name('wishlist-add');
        Route::delete('/wishlist/remove',  'wishlistRemove')->name('wishlist-remove');
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
    Route::prefix('slides')->name('-slides')->group(function () {
        Route::get('', [SlideController::class, 'index']);
        Route::post('', [SlideController::class, 'store'])->name('-store');
        Route::patch('{slide}', [SlideController::class, 'update'])->name('-update');
        Route::delete('{slide}', [SlideController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{slide}', [SlideController::class, 'publish'])->name('-publish');
        Route::patch('promote/{slide}', [SlideController::class, 'promote'])->name('-promote');
    });
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
    Route::prefix('partners')->name('-partners')->group(function () {
        Route::get('', [PartnerController::class, 'index']);
        Route::post('', [PartnerController::class, 'store'])->name('-store');
        Route::patch('{partner}', [PartnerController::class, 'update'])->name('-update');
        Route::delete('{partner}', [PartnerController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{partner}', [PartnerController::class, 'publish'])->name('-publish');
        Route::patch('promote/{partner}', [PartnerController::class, 'promote'])->name('-promote');
    });
    Route::prefix('brands')->name('-brands')->group(function () {
        Route::get('', [BrandController::class, 'index']);
        Route::post('', [BrandController::class, 'store'])->name('-store');
        Route::patch('{brand}', [BrandController::class, 'update'])->name('-update');
        Route::delete('{brand}', [BrandController::class, 'destroy'])->name('-destroy');
    });
    Route::prefix('news_articles')->name('-news_articles')->group(function () {
        Route::get('', [NewsArticleController::class, 'index']);
        Route::post('', [NewsArticleController::class, 'store'])->name('-store');
        Route::patch('{news_article}', [NewsArticleController::class, 'update'])->name('-update');
        Route::delete('{news_article}', [NewsArticleController::class, 'destroy'])->name('-destroy');
        Route::patch('publish/{news_article}', [NewsArticleController::class, 'publish'])->name('-publish');
        Route::patch('promote/{news_article}', [NewsArticleController::class, 'promote'])->name('-promote');
    });

    Route::prefix('quotes')->name('-quotes')->group(function () {
        Route::get('', [QuoteController::class, 'index']);
        Route::post('', [QuoteController::class, 'store'])->name('-store');
        Route::patch('{quote}', [QuoteController::class, 'update'])->name('-update');
        Route::delete('{quote}', [QuoteController::class, 'destroy'])->name('-destroy');
        Route::patch('status/{quote}', [QuoteController::class, 'updateStatus'])->name('-status');
        Route::patch('items/{item}', [QuoteController::class, 'updateQuoteItem'])->name('-items-update');
    });

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
