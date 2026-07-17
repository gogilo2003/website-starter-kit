<?php

namespace Gogilo\News;

use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/news.php');

        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        if (! $this->app->bound(MenuRegistry::class)) {
            return;
        }

        $registry = $this->app->make(MenuRegistry::class);

        // Register Admin Dashboard Menu Item
        $registry->register('admin', new MenuItem(
            name: 'dashboard-news_articles',
            caption: 'News Articles',
            icon: 'document-text',
            route: 'dashboard-news_articles',
            order: 75,
        ));

        // Register Public Menu Item
        $registry->register('public', new MenuItem(
            name: 'news',
            caption: 'News & Updates',
            route: 'news',
            order: 35,
        ));
    }
}
