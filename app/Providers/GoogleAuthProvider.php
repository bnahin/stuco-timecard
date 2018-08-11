<?php

namespace App\Providers;

use App\Common\Bnahin\EcrchsAuth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class GoogleAuthProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // View Composer for Auth User
        View::composer('*', function ($view) {
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
            } else {
                if (Auth::check()) {
                    $user = Auth::user();
                }
            }
            $view->with('user', $user);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
