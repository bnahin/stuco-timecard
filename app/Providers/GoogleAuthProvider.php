<?php

namespace App\Providers;

use App\Common\Bnahin\EcrchsAuth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        // View Composers
        View::composer('*', function (\Illuminate\View\View $view) {
            /** Auth User */
            $user = null;
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
            } else {
                if (Auth::check()) {
                    $user = Auth::guard('user')->user();
                }
            }

            /** Clubs */
            if ($user) {
                $clubs = $user->clubs;

                $clubId = getClubId();
                $currClub = \App\Club::find($clubId);
                $clubCode = $currClub->join_code;
                $clubName = $currClub->club_name;

                $view->with(
                    compact('user', 'clubName',
                        'clubs', 'clubId',
                        'currClub', 'clubCode'
                    ));
            }
        });

        View::composer('clubselect', function (\Illuminate\View\View $view) {
            $view->with('auth', Session::get('temp-auth'));
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
