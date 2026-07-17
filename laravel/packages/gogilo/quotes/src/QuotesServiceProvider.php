<?php

namespace Gogilo\Quotes;

use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Gogilo\Quotes\Interfaces\Repositories\QuoteRepositoryInterface;
use Gogilo\Quotes\Repositories\QuoteRepository;
use Illuminate\Support\ServiceProvider;

class QuotesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(QuoteRepositoryInterface::class, QuoteRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/quotes.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'quotes');

        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        if (! $this->app->bound(MenuRegistry::class)) {
            return;
        }

        $registry = $this->app->make(MenuRegistry::class);

        // Register Quotes admin menu item
        $registry->register('admin', new MenuItem(
            name: 'dashboard-quotes',
            caption: 'Quotes',
            icon: 'clipboard',
            route: 'dashboard-quotes',
            order: 20,
        ));
    }
}
