<?php

namespace App\Http\Controllers;

use App\Common\Bnahin\EcrchsAuth;
use App\StudentInfo;
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

        //Associate student info
        $student = StudentInfo::where('email', $apiUser->email);
        if ($student->exists()) {
            $student->first()->user()->associate($user);
        }

        //Associate hours
        $newUser = User::where('google_id',$apiUser->id)->first();
        if (!$newUser->student) {
            abort(403,'User is not in the Aeries database.');
        }
        $hours = \App\Hour::where('student_id', $newUser->student->student_id)
            ->whereNull('user_id');
        if ($hours->count()) {
            $hoursRes = $hours->get();
            foreach ($hoursRes as $hour) {
                $hour->user_id = $user->id;
                $hour->save();
            }
        }

        Auth::login($user, true);

        return redirect()->route('home');
    }
}
