<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Common\Bnahin\EcrchsAuth;
use App\StudentInfo;
use Illuminate\Http\Request,
    Laravel\Socialite\Facades\Socialite,
    App\User,
    Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        /*
        $loginAs = "user";
        Auth::logout();
        Session::invalidate();
        Auth::guard($loginAs)->login(
            ($loginAs == "user") ?
                User::inRandomOrder()->first() : Admin::find(1));
        */


        return redirect('/');
        $apiUser = $this->api->getUser();

        //Admin?
        $admin = Admin::where('email', $apiUser->email);
        if ($admin->exists()) {
            $admin = $admin->first();
            //Admin!
            $admin->google_id = $apiUser->id;
            $admin->saveOrFail();

            Auth::guard('admin')->login($admin, true);

            return redirect()->route('home');
        }

        $user = User::where('email', $apiUser->email);
        if ($user->exists()) {
            //Exists!
            $user->update([
                'google_id'  => $apiUser->id,
                'domain'     => $apiUser->hd,
                'email'      => $apiUser->email,
                'first_name' => $apiUser->given_name,
                'last_name'  => $apiUser->family_name
            ]);
        } else {
            //Create!
            $user = new User([
                'google_id'  => $apiUser->id,
                'domain'     => $apiUser->hd,
                'email'      => $apiUser->email,
                'first_name' => $apiUser->given_name,
                'last_name'  => $apiUser->family_name
            ]);
        }

        //Associate student info
        $student = StudentInfo::where('email', $apiUser->email);
        if ($student->exists()) {
            $student->first()->user()->associate($user);
        }

        //Associate hours
        $newUser = User::where('google_id', $apiUser->id)->first();
        if (!$newUser->student) {
            abort(403, 'User is not in the Aeries database.');
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

        Session::put('club-id', 1); //TODO use ClubSessionManager
        Auth::login($user, true);

        return redirect()->route('home');
    }
}
