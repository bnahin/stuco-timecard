<?php

namespace App\Http\Controllers;

use App\Event;
use App\Hour;
use App\Http\Requests\StoreHoursRequest;
use App\StudentInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HoursController extends Controller
{
    /**
     * Store new hour submission
     * "Add New Activity"
     *
     * @param \App\Http\Requests\StoreHoursRequest $request
     *
     * @return array
     * @throws \Throwable
     */
    public function store(StoreHoursRequest $request)
    {
        $req = $request->validated();

        $stuid = $req['id'];
        $event = $req['event'];
        $comments = $req['comments'] ?? null; //Comments might be blank

        $hour = new Hour;
        $hour->student_id = $stuid;
        $hour->event_id = $event;
        $hour->start_time = Carbon::now();
        $hour->comments = $comments;
        #$hour->saveOrFail();

        $user = User::where('student_id', $stuid);
        if ($user->exists()) {
            //Associate user
            $hour->user_id = $user->first()->id;
            $hour->saveOrFail();
        }

        $name = StudentInfo::where('student_id', $hour->student_id)->first()->full_name;
        $event = $hour->getEventName();
        if (Auth::user()->isAdmin()) {
            log_action("Clocked out $name for $event");
        }
        else {
            log_action("Clocked out for $event");
        }

        return ['success' => true, 'messsage' => $user->first()->first_name . " has been clocked out."];


    }

    public function delete(Request $request)
    {
        $stuid = Auth::user()->student->student_id;
        if (Hour::isClockedOut($stuid)) {
            //Delete hour
            try {
                Hour::getClockData($stuid)->delete();
                log_action("Deleted time punch");
            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }

            return response()->json(['success' => true]);
        } else {
            return abort(422, 'You are not clocked out.');
        }
    }
}
