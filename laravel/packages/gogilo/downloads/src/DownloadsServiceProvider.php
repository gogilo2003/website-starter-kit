<?php

namespace Gogilo\Downloads;

use Gogilo\Downloads\Models\DownloadCategory;
use Gogilo\Downloads\Repositories\DownloadCategoryRepository;
use Gogilo\Downloads\Repositories\DownloadCategoryRepositoryInterface;
use Gogilo\Downloads\Repositories\DownloadRepository;
use Gogilo\Downloads\Repositories\DownloadRepositoryInterface;
use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Illuminate\Support\ServiceProvider;

class DownloadsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DownloadRepositoryInterface::class, DownloadRepository::class);
        $this->app->bind(DownloadCategoryRepositoryInterface::class, DownloadCategoryRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/downloads.php');

        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        if (! $this->app->bound(MenuRegistry::class)) {
            return;
        }

        $registry = $this->app->make(MenuRegistry::class);

        $registry->register('admin', new MenuItem(
            name: 'dashboard-downloads-categories',
            caption: 'Downloads',
            icon: 'downloads',
            route: 'dashboard-downloads-categories',
            order: 90,
        ));

        $registry->register('public', new MenuItem(
            name: 'downloads',
            caption: 'Downloads',
            route: 'downloads',
            order: 40,
            children: fn () => DownloadCategory::query()
                ->get()
                ->map(fn ($item) => [
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'caption' => $item->name,
                    'description' => $item->description,
                ])
                ->all(),
        ));
    }
}
