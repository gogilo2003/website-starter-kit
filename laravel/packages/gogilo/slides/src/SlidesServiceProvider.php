<?php

namespace Gogilo\Slides;

use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Illuminate\Support\ServiceProvider;

class SlidesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/slides.php');

        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        if (! $this->app->bound(MenuRegistry::class)) {
            return;
        }

        $registry = $this->app->make(MenuRegistry::class);

        $registry->register('admin', new MenuItem(
            name: 'dashboard-slides',
            caption: 'Slides',
            icon: 'image',
            route: 'dashboard-slides',
            order: 50,
        ));
    }
}
