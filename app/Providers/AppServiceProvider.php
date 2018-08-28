<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        /** Custom Blade Directives */
        Blade::if('admin', function () {
            return isAdmin();
        });
        Blade::if('route', function ($route) {
            return Route::currentRouteName() === $route;
        });

        /** View Composers */
        View::composer('*', function (\Illuminate\View\View $view) {
            if (isAdmin()) {
                $adminBadge = \App\Hour::marked()->count();
            } else {
                $adminBadge = 0;
            }
            $view->with(compact('adminBadge'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
