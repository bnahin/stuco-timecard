<?php
/**
 * ECRCHS Google SSO Wrapper
 * @author Blake Nahin <bnahin@live.com>
 */

namespace App\Common\Bnahin;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Laravel\Socialite\Facades\Socialite;

class EcrchsAuth
{
    private $guzzle;
    public $user;

    public function __construct()
    {
        $this->guzzle = new Client(['base_uri' => 'https://www.googleapis.com/oauth2/v2/']);
    }

    public function getUser()
    {
        if (App::isLocal()) {
            $headers = [
                'Authorization' => 'Bearer ' . config('services.google.client_secret')
            ];

            $response = $this->guzzle->get('userinfo', ['headers' => $headers])->getBody()
                ->getContents();

            $user = json_decode($response, true);
            $this->user = $user;

        } else {
            $this->user = Socialite::driver('google')->user();
        }

        return (object)$this->user;
    }
}