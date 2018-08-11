<?php

namespace App\Http\Controllers;

use App\Hour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Event;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clockedOut = false;

        if (Auth::guard('user')->check()) {
            $stuid = Auth::user()->student->student_id;
            if (Hour::isClockedOut($stuid)) {
                $clockedOut = true;
                $data = Hour::getClockData($stuid);
                $eventCount = Hour::where('user_id', Auth::id())->count();
            }

        }
        $events = Event::active()->get();

        return view('home', compact('events', 'clockedOut', 'data', 'eventCount'));
    }
}