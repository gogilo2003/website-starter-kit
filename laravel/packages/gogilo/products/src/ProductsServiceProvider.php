<?php

namespace Gogilo\Products;

use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Gogilo\Products\Repositories\BrandRepository;
use Gogilo\Products\Repositories\BrandRepositoryInterface;
use Gogilo\Products\Repositories\ProductCategoryRepository;
use Gogilo\Products\Repositories\ProductCategoryRepositoryInterface;
use Gogilo\Products\Repositories\ProductRepository;
use Gogilo\Products\Repositories\ProductRepositoryInterface;
use Gogilo\Products\Services\ProductCategoryService;
use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/products.php');

        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        if (! $this->app->bound(MenuRegistry::class)) {
            return;
        }

        $registry = $this->app->make(MenuRegistry::class);

        $registry->register('admin', new MenuItem(
            name: 'dashboard-products-categories',
            caption: 'Products',
            icon: 'product',
            route: 'dashboard-products-categories',
            altNames: ['dashboard-products'],
            order: 70,
        ));

        $registry->register('admin', new MenuItem(
            name: 'dashboard-brands',
            caption: 'Brands',
            icon: 'rectangle-group',
            route: 'dashboard-brands',
            order: 60,
        ));

        $registry->register('public', new MenuItem(
            name: 'products',
            caption: 'Products',
            route: 'products',
            order: 30,
            children: fn () => app(ProductCategoryService::class)
                ->getAllProductCategories(['filters' => ['published' => 1]], true),
        ));
    }
}
