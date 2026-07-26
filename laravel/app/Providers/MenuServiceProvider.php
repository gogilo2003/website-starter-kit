<?php

namespace App\Providers;

use MeaCms\Menu\MenuItem;
use MeaCms\Menu\MenuRegistry;
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
                name: 'contact',
                caption: 'Contact Us',
                route: 'contact',
                order: 50,
            ),
        ]);
    }
}
