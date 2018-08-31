<?php

namespace App\Http\Controllers;

use App\Club;
use App\Setting;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
