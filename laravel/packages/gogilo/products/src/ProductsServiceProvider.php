<?php

namespace Gogilo\Products;

use Illuminate\Support\ServiceProvider;
use Gogilo\Products\Console\InstallProductsCommand;

class ProductsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $basePath = dirname(__DIR__);

        $this->publishes([
            $basePath . '/stubs/config/products.php' => config_path('products.php'),
        ], 'products-config');

        $this->publishes([
            $basePath . '/stubs/routes/products.php' => base_path('routes/products.php'),
        ], 'products-routes');

        $this->publishes([
            $basePath . '/stubs/app/Interfaces/Repositories/ProductRepositoryInterface.php' => app_path('Interfaces/Repositories/ProductRepositoryInterface.php'),
            $basePath . '/stubs/app/Repositories/ProductRepository.php' => app_path('Repositories/ProductRepository.php'),
            $basePath . '/stubs/app/Http/Controllers/ProductController.php' => app_path('Http/Controllers/ProductController.php'),
            $basePath . '/stubs/app/Models/Product.php' => app_path('Models/Product.php'),
        ], 'products-backend');

        $this->publishes([
            $basePath . '/stubs/database/migrations/2024_01_31_001129_create_products_table.php' => database_path('migrations/2024_01_31_001129_create_products_table.php'),
            $basePath . '/stubs/database/migrations/2025_10_28_040329_create_product_categories_table.php' => database_path('migrations/2025_10_28_040329_create_product_categories_table.php'),
            $basePath . '/stubs/database/migrations/2025_11_03_225339_alter_products_table_add_price_and_attributes_fields.php' => database_path('migrations/2025_11_03_225339_alter_products_table_add_price_and_attributes_fields.php'),
            $basePath . '/stubs/database/migrations/2025_11_12_005428_add_brand_id_to_products_table.php' => database_path('migrations/2025_11_12_005428_add_brand_id_to_products_table.php'),
        ], 'products-migrations');

        $this->publishes([
            $basePath . '/stubs/database/seeders/ProductCategorySeeder.php' => database_path('seeders/ProductCategorySeeder.php'),
            $basePath . '/stubs/database/seeders/ProductsDatabaseSeeder.php' => database_path('seeders/ProductsDatabaseSeeder.php'),
        ], 'products-seeders');

        $this->publishes([
            $basePath . '/stubs/resources/js/interfaces.ts' => resource_path('js/interfaces.ts'),
            $basePath . '/stubs/resources/js/Pages/Products/Index.vue' => resource_path('js/Pages/Products/Index.vue'),
            $basePath . '/stubs/resources/js/Pages/Product.vue' => resource_path('js/Pages/Product.vue'),
            $basePath . '/stubs/resources/js/Pages/Dashboard/Products/Categories.vue' => resource_path('js/Dashboard/Products/Categories.vue'),
            $basePath . '/stubs/resources/js/Pages/Dashboard/Products/Index.vue' => resource_path('js/Dashboard/Products/Index.vue'),
            $basePath . '/stubs/resources/js/Components/Products/ProductCard.vue' => resource_path('js/Components/Products/ProductCard.vue'),
            $basePath . '/stubs/resources/js/Components/Products/ProductCategoryCard.vue' => resource_path('js/Components/Products/ProductCategoryCard.vue'),
        ], 'products-vue');

        $this->publishes([
            $basePath . '/stubs/config/products.php' => config_path('products.php'),
            $basePath . '/stubs/routes/products.php' => base_path('routes/products.php'),
            $basePath . '/stubs/app/Interfaces/Repositories/ProductRepositoryInterface.php' => app_path('Interfaces/Repositories/ProductRepositoryInterface.php'),
            $basePath . '/stubs/app/Repositories/ProductRepository.php' => app_path('Repositories/ProductRepository.php'),
            $basePath . '/stubs/app/Http/Controllers/ProductController.php' => app_path('Http/Controllers/ProductController.php'),
            $basePath . '/stubs/app/Models/Product.php' => app_path('Models/Product.php'),
            $basePath . '/stubs/database/migrations/2024_01_31_001129_create_products_table.php' => database_path('migrations/2024_01_31_001129_create_products_table.php'),
            $basePath . '/stubs/database/migrations/2025_10_28_040329_create_product_categories_table.php' => database_path('migrations/2025_10_28_040329_create_product_categories_table.php'),
            $basePath . '/stubs/database/migrations/2025_11_03_225339_alter_products_table_add_price_and_attributes_fields.php' => database_path('migrations/2025_11_03_225339_alter_products_table_add_price_and_attributes_fields.php'),
            $basePath . '/stubs/database/migrations/2025_11_12_005428_add_brand_id_to_products_table.php' => database_path('migrations/2025_11_12_005428_add_brand_id_to_products_table.php'),
            $basePath . '/stubs/database/seeders/ProductCategorySeeder.php' => database_path('seeders/ProductCategorySeeder.php'),
            $basePath . '/stubs/database/seeders/ProductsDatabaseSeeder.php' => database_path('seeders/ProductsDatabaseSeeder.php'),
            $basePath . '/stubs/resources/js/Pages/Products/Index.vue' => resource_path('js/Pages/Products/Index.vue'),
            $basePath . '/stubs/resources/js/Components/Products/ProductCard.vue' => resource_path('js/Components/Products/ProductCard.vue'),
        ], 'products-package');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallProductsCommand::class,
            ]);
        }
    }
}
