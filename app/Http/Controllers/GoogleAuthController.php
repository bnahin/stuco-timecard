<?php

namespace App\Http\Controllers;

use App\Common\Bnahin\EcrchsAuth;
use Illuminate\Http\Request,
    Laravel\Socialite\Facades\Socialite,
    App\User,
    Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class GoogleAuthController extends Controller
{
    protected $api;

    public function __construct(EcrchsAuth $api)
    {
        $this->api = $api;
    }

    /**
     * Redirect to Google SSO.
     */
    public function redirect()
    {
        if (App::isLocal()) {
            return redirect()->route('oauth-callback');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google SSO Callback
     */
    public function handle()
    {
        $apiUser = $this->api->getUser();

        $user = User::updateOrCreate([
            'google_id' => $apiUser->id,
        ], [
            'domain'     => $apiUser->hd,
            'email'      => $apiUser->email,
            'first_name' => $apiUser->given_name,
            'last_name'  => $apiUser->family_name
        ]);

        Auth::login($user, true);

        return redirect()->route('home');
    }
}
