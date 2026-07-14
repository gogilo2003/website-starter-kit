<?php

namespace Gogilo\Menu;

use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MenuRegistry::class, fn () => new MenuRegistry);
    }

    public function boot(): void
    {
        //
    }
}
