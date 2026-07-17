<?php

namespace Gogilo\PageSections;

use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Gogilo\PageSections\Interfaces\Repositories\ElementRepositoryInterface;
use Gogilo\PageSections\Interfaces\Repositories\PageSectionRepositoryInterface;
use Gogilo\PageSections\Repositories\ElementRepository;
use Gogilo\PageSections\Repositories\PageSectionRepository;
use Illuminate\Support\ServiceProvider;

class PageSectionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ElementRepositoryInterface::class, ElementRepository::class);
        $this->app->bind(PageSectionRepositoryInterface::class, PageSectionRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/page-sections.php');

        $this->registerMenu();
    }

    protected function registerMenu(): void
    {
        if (! $this->app->bound(MenuRegistry::class)) {
            return;
        }

        $registry = $this->app->make(MenuRegistry::class);

        // Register Page Elements admin menu item
        $registry->register('admin', new MenuItem(
            name: 'dashboard-elements',
            caption: 'Page Elements',
            icon: 'document-text',
            route: 'dashboard-elements',
            order: 30,
        ));

        // Register Page Sections admin menu item
        $registry->register('admin', new MenuItem(
            name: 'dashboard-page-sections',
            caption: 'Page Sections',
            icon: 'document-text',
            route: 'dashboard-page-sections',
            order: 40,
        ));
    }
}
