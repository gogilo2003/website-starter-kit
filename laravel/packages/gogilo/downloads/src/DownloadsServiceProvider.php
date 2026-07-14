<?php

namespace Gogilo\Downloads;

use Illuminate\Support\ServiceProvider;

class DownloadsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/downloads.php',
            'downloads'
        );

        $this->app->singleton(DownloadManager::class, function ($app) {
            return new DownloadManager(
                $app->make(\Illuminate\Contracts\Filesystem\Factory::class),
                $app->make('config')->get('downloads', [])
            );
        });

        $this->app->alias(DownloadManager::class, 'downloads');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $basePath = __DIR__;

        // Publish configuration
        $this->publishes([
            $basePath . '/Config/downloads.php' => config_path('downloads.php'),
        ], 'downloads-config');

        // Register routes
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $basePath . '/Console/stubs/api.php' => base_path('routes/downloads-api.php'),
                $basePath . '/Console/stubs/web.php' => base_path('routes/downloads-web.php'),
            ], 'downloads-routes');
        }

        $this->loadRoutesFrom($basePath . '/Routes/downloads.php');

        // Register middleware
        $this->app['router']->aliasMiddleware('download.throttle', Middleware\DownloadThrottleMiddleware::class);
        $this->app['router']->aliasMiddleware('download.permission', Middleware\DownloadPermissionMiddleware::class);

        // Register views
        $this->loadViewsFrom($basePath . '/Resources/views', 'downloads');

        // Register migrations
        $this->loadMigrationsFrom($basePath . '/Database/Migrations');
    }
}