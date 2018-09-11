<?php

namespace App\Providers;

use App\Announcement;
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
            $adminBadge = (isAdmin()) ? \App\Hour::marked()->count() : 0;
            $announcementsBadge = Announcement::recent()->count();

            if (getClubId()) {
                $settings = \App\Setting::findOrFail(getClubId())->first();
            }

            $view->with(compact('adminBadge', 'settings', 'announcementsBadge'));
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
