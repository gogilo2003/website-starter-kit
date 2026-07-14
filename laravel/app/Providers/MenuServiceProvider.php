<?php

namespace App\Providers;

use App\Services\ProductCategoryService;
use Gogilo\Menu\MenuItem;
use Gogilo\Menu\MenuRegistry;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function boot(MenuRegistry $menu): void
    {
        $this->registerAdminMenu($menu);
        $this->registerPublicMenu($menu);
    }

    protected function registerAdminMenu(MenuRegistry $menu): void
    {
        $menu->registerMany('admin', [
            new MenuItem(
                name: 'dashboard',
                caption: 'Dashboard',
                icon: 'dashboard',
                route: 'dashboard',
                order: 10,
            ),
            new MenuItem(
                name: 'dashboard-quotes',
                caption: 'Quotes',
                icon: 'clipboard',
                route: 'dashboard-quotes',
                order: 20,
            ),
            new MenuItem(
                name: 'dashboard-page-sections',
                caption: 'Page Sections',
                icon: 'sections',
                route: 'dashboard-page-sections',
                order: 30,
            ),
            new MenuItem(
                name: 'dashboard-elements',
                caption: 'Elements',
                icon: 'elements',
                route: 'dashboard-elements',
                order: 40,
            ),
            new MenuItem(
                name: 'dashboard-slides',
                caption: 'Slides',
                icon: 'image',
                route: 'dashboard-slides',
                order: 50,
            ),
            new MenuItem(
                name: 'dashboard-brands',
                caption: 'Brands',
                icon: 'rectangle-group',
                route: 'dashboard-brands',
                order: 60,
            ),
            new MenuItem(
                name: 'dashboard-products-categories',
                caption: 'Products',
                icon: 'product',
                route: 'dashboard-products-categories',
                altNames: ['dashboard-products'],
                order: 70,
            ),
            new MenuItem(
                name: 'dashboard-partners',
                caption: 'Partners',
                icon: 'partners',
                route: 'dashboard-partners',
                order: 80,
            ),
            new MenuItem(
                name: 'dashboard-migrations',
                caption: 'Migrations',
                icon: 'command-line',
                route: 'dashboard-migrations',
                order: 100,
            ),
        ]);
    }

    protected function registerPublicMenu(MenuRegistry $menu): void
    {
        $menu->registerMany('public', [
            new MenuItem(
                name: 'home',
                caption: 'Home',
                route: 'home',
                order: 10,
            ),
            new MenuItem(
                name: 'about',
                caption: 'About Us',
                route: 'about',
                order: 20,
            ),
            new MenuItem(
                name: 'products',
                caption: 'Products',
                route: 'products',
                order: 30,
                children: fn () => app(ProductCategoryService::class)
                    ->getAllProductCategories(['filters' => ['published' => 1]], true),
            ),
            new MenuItem(
                name: 'contact',
                caption: 'Contact Us',
                route: 'contact',
                order: 50,
            ),
        ]);
    }
}
