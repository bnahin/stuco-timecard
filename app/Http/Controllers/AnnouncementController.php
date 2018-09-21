<?php

namespace App\Http\Controllers;

use App\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = Announcement::paginate(5);

        return view('pages.announcements', compact('announcements'));
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
     * @return \Response
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'message' => 'required'
        ]);

        $title = $request->title;
        $message = $request->message;

        $post = new Announcement;
        $post->post_title = $title;
        $post->post_body = $message;
        $post->admin_id = Auth::user()->id;
        $post->club_id = getClubId();
        $post->is_global = false;
        $post->email_sent = false;
        $post->saveOrFail();

        //TODO: Send Emails

        return back()->with('newPost', true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Announcement        $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Announcement $announcement
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        //
    }
}
