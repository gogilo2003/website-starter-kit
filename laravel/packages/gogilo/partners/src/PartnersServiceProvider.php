<?php

namespace Gogilo\Partners;

use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Illuminate\Support\ServiceProvider;

class PartnersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/partners.php');

        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        if (! $this->app->bound(MenuRegistry::class)) {
            return;
        }

        $registry = $this->app->make(MenuRegistry::class);

        // Register Partners admin menu item
        $registry->register('admin', new MenuItem(
            name: 'dashboard-partners',
            caption: 'Partners',
            icon: 'partners',
            route: 'dashboard-partners',
            order: 80,
        ));
    }
}
