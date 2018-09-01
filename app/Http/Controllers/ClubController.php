<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Club;
use App\Helpers\AuthHelper;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClubController extends Controller
{
    /**
     * Club Select page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Session::has('club-id')) {
            return redirect('/');
        }
        if(!Session::has('temp-auth')) {
            AuthHelper::logout();
            return redirect()->route('login');
            //return redirect()->route('logout');
        }
        $auth = Session::get('temp-auth');
        $clubs = [];

        $user = User::where('email', $auth->email);
        if ($user->exists() && $user->first()->clubs) {
            //Student clubs
            $clubs['student'] = $user->first()->clubs;
        }

        $admin = Admin::where('email', $auth->email);
        if ($admin->exists() && $admin->first()->clubs) {
            //Admin clubs
            $clubs['admin'] = $admin->first()->clubs;
        }

        return view('clubselect')->with(['clubSelect' => $clubs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Club $club
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Club $club)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Club $club
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Club $club)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function update(Request $request)
    {
        $request->validate([
            'master'        => 'required|boolean',
            'allowDeletion' => 'required|boolean',
            'allowMark'     => 'required|boolean',
            'allowComments' => 'required|boolean'
        ]);

        $desc = $request->desc;
        $allowDeletion = $request->allowDeletion;
        $allowMark = $request->allowMark;
        $allowComments = $request->allowComments;
        $master = $request->master;

        $settings = Setting::find(getClubId())->first();
        $settings->club_desc = $desc;
        $settings->allow_mark = $allowMark;
        $settings->allow_delete = $allowDeletion;
        $settings->allow_comments = $allowComments;
        $settings->master = $master;
        $settings->saveOrFail();

        return response()->json(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Club $club
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Club $club)
    {
        //
    }
}
