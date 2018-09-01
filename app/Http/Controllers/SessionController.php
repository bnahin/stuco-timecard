<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Club;
use App\Helpers\AuthHelper;
use App\StudentInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('login');
    }

    public function switchClub(Club $club)
    {
        if (!Session::has('temp-auth') || Auth::check()) {
            return redirect()->route('logout');
        }

        $auth = Session::get('temp-auth');
        AuthHelper::logout(); //Logs user out and removes session variables

        //Admin?
        $admin = Admin::where('email', $auth->email);
        if ($admin->exists()
            && $admin->first()->clubs()->where('clubs.id', $club->id)->exists()) {
            //Admin!
            $admin = $admin->first();
            $admin->google_id = $auth->id;
            $admin->save();

            Session::put('club-id', $club->id);
            Auth::guard('admin')->login($admin, true);

            return redirect()->route('home');
        }

        $user = User::where('email', $auth->email);
        if ($user->exists()
            && $user->first()->clubs()->where('clubs.id', $club->id)->exists()) {
            //Exists!
            $user->update([
                'google_id'  => $auth->id,
                'domain'     => $auth->hd,
                'email'      => $auth->email,
                'first_name' => $auth->given_name,
                'last_name'  => $auth->family_name
            ]);

            //Associate student info
            $student = StudentInfo::where('email', $auth->email);
            if ($student->exists()) {
                $student->first()->update(['user_id' => $user->first()->id]);
            }

            //Associate hours
            $newUser = User::where('google_id', $auth->id)->first();
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

            Session::put('club-id', $club->id);
            Auth::guard('user')->login($newUser, true);

            return redirect()->route('home');
        }
        /*
        //Create!
        $user = new User([
            'google_id'  => $auth->id,
            'domain'     => $auth->hd,
            'email'      => $auth->email,
            'first_name' => $auth->given_name,
            'last_name'  => $auth->family_name
        ]);*/

        //Not a member of the club, how are they here?
        //User creation occurs when joining w/ join code or being assigned

        abort(402, "Unable to switch session.");
    }

}
