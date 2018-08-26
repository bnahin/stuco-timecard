<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use App\BlockedUser;
use App\Event;
use App\Hour;
use App\StudentInfo;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{

    public function index($page = null)
    {
        $page = $page ?: 'assign';

        return view('pages.admin')->with(
            [
                'data' => $this->getViewData($page),
                'page' => $page
            ]);
    }

    private function getViewData($page)
    {
        $data = [];
        switch ($page) {
            case 'assign':
                $data = $this->getAssignedStudents();
                break;
            case 'blocked':
                $data = BlockedUser::with('user')->where('club_id', getClubId())->get();
                break;
            case 'hourstats':
                $data['members'] = $this->getAssignedStudents()
                    ->count();
                break;
            case 'marked':
                $data['hours'] = Hour::marked()->get();
                $data['events'] = Event::active()->get();
                break;
            case 'events':
                $data = Event::all();
                break;
            case 'system':
                $data = ActivityLog::all();
                break;
        }

        return $data;
    }

    public function processEnrolledStudentsTable(Request $request)
    {
        $students = StudentInfo::select(['student_id', 'first_name', 'last_name', 'grade', 'email'])->get();

        return DataTables::of($students)->make();
    }

    private function getAssignedStudents()
    {
        $clubid = getClubId();
        $students = User::whereHas('clubs', function ($q) use ($clubid) {
            return $q->where('clubs.id', $clubid);
        });

        return $students->notBlockedFrom($clubid)
            ->orderBy('last_name', 'asc')->get();
    }

    public function assignStudent(Request $request)
    {
        $request->validate([
            'id' => 'required|min:6'
        ]);

        $id = ucwords($request->id);
        $message = null;

        $studentInfo = StudentInfo::where('student_id', $id)
            ->orWhere(\DB::raw("CONCAT_WS(' ',first_name,last_name)"), $id);

        if (!$studentInfo->exists()) {
            //Does not exists in student database
            return response()->json(['status' => 'error', 'message' => 'Could not find student with that name or ID.']);
        }

        $student = $studentInfo->with('user')->first();
        if ($student->user) {
            //User model exists, attach club.
            $user = User::where('student_info_id', $student->id);

            if ($user->first()->clubs()->where('clubs.id', getClubId())->exists()) {
                //Already has club
                return response()->json([
                    'status'  => 'error',
                    'message' => 'The student is already assigned to this club.'
                ]);
            } else {
                $user->first()->clubs()->attach(getClubId());

                log_action("Assigned student {$user->full_name}");

                return response()->json(['status' => 'success', 'message' => $user->first()]);
            }
        }
        //User does not exist, create!
        $newUser = User::create([
            'google_id'       => null,
            'student_info_id' => $student->id,
            'first_name'      => $student->first_name,
            'last_name'       => $student->last_name,
            'email'           => $student->email,
            'domain'          => 'ecrchs.org'
        ]);
        $student->update(['user_id' => $newUser->id]);
        $newUser->clubs()->attach(getClubId());

        log_action("Assigned student {$newUser->full_name}");

        return response()->json(['status' => 'success', 'message' => $student ?: null]);
        //Create user model and attach
        //Null google id

        //Without a user model the admin table will break!
    }

    public function unblock(Request $request)
    {
        $request->validate([
            'id' => 'required|int'
        ]);

        $id = $request->id;
        try {
            $blockedUser = BlockedUser::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()
                ->json(['status' => 'error', 'message' => 'Already unblocked']);
        }

        log_action("Unblocked student {$blockedUser->user->full_name}");
        $blockedUser->delete();

        return response()->json(['status' => 'success']);

        //Already unblocked
    }

    public function dropStudent(Request $request)
    {
        $id = $request->id;

        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()
                ->json(['status' => 'error', 'message' => 'Already dropped']);
        }

        $user->clubs()->detach(getClubId());
        $user->blocks()->create(['club_id' => getClubId()]);

        log_action("Dropped student " . $user->full_name);

        return response()->json(['status' => 'success']);
    }

    public function purgeStudents()
    {
        $students = User::whereHas('clubs', function ($query) {
            $query->where('club_id', getClubId());
        });
        if ($students->exists()) {
            //Purge
            foreach ($students->get() as $student) {
                $student->clubs()->detach(getClubId());
            }

            log_action("Purged all students");

            return response()->json(['status' => 'success']);
        }

        //No students exist
        return response()->json(['status' => 'error', 'message' => 'No students in club.']);
    }

    public function getHourData(Request $request)
    {
        $request->validate([
            'id' => 'required|int'
        ]);

        $id = $request->id;
        $hour = Hour::find($id);

        if (!$hour->count()) {
            return response()->json(['status' => 'error', 'message' => 'Timepunch does not exist.']);
        }

        //TODO: Push Data
        $data = [
            'name'      => $hour->user->full_name,
            'comments'  => $hour->comments ?: '<em>No Comments</em>',
            'date'      => $hour->start_time->format('m/d/Y'),
            'startTime' => $hour->start_time->format('h:iA'),
            'endTime'   => $hour->end_time->format('h:iA'),
            'event'     => $hour->event_id
        ];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function changeEventsOrder(Request $request)
    {
        $request->validate([
            'thisId' => 'int|required|exists:events,id',
            'nextId' => 'int|exists:events,id',
            'prevId' => 'int|exists:events,id',
            'dir'    => [
                Rule::in(['down', 'up'])
            ]
        ]);
        if ($request->dir == 'up') {
            //Swap this and previous event ordere
            Event::find($request->thisId)->decrement('order');
            Event::find($request->prevId)->increment('order');
        } else {
            //Swap this and next event order
            Event::find($request->thisId)->increment('order');
            Event::find($request->nextId)->decrement('order');
        }

        return response()->json(['bottom' => false, 'top' => false]);
    }

    public function updateEventName(Request $request)
    {
        $request->validate([
            'id'  => 'required|exists:events',
            'val' => 'required'
        ]);

        $event = Event::find($request->id);
        $old = $event->event_name;
        $event->event_name = $request->val;
        try {
            $event->saveOrFail();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        log_action("Changed event \"$old\" to \"" . Event::find($request->id)->event_name . "\"");

        return response()->json(['status' => 'success']);
    }

    public function toggleVisibility(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:events'
        ]);

        $event = Event::find($request->id);
        $event->is_active = !$event->is_active;
        try {
            $event->saveOrFail();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        log_action('Changed event "' . Event::find($request->id)->event_name . '" visibility to ' . (($event->is_active) ? 'Active' : 'Hidden'));

        return response()->json(['status' => 'success']);
    }

    public function deleteEvent(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:events'
        ]);

        log_action('Deleted event "' . Event::find($request->id)->event_name . '"');

        $event = Event::find($request->id);
        $event->delete();

        return response()->json(['status' => 'success']);
    }

    public function purgeEvent(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:events'
        ]);

        try {
            $hours = Hour::where('event_id', $request->id)->delete();
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        log_action('Purged event "' . Event::find($request->id)->event_name . '"');

        return response()->json(['status' => 'success']);
    }

    public function createEvent(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:events,event_name'
        ]);

        $event = new Event;
        $event->event_name = $request->name;
        $event->order = Event::getLast()->id + 1;
        $event->is_active = true;
        $event->club_id = getClubId();
        try {
            $event->saveOrFail();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'success', 'id' => $event->id]);
    }

    public function undoMark(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:hours'
        ]);

        $hour = Hour::find($request->id);
        $hour->needs_review = false;
        try {
            $hour->saveOrFail();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'success']);
    }

    public function updateHour(Request $request)
    {
        $request->validate([
            'id'         => 'required|exists:hours',
            'event'      => 'required|exists:events,id',
            'date'       => 'required|date|date_format:m/d/Y',
            'start_time' => 'required|before:end_time',
            'end_time'   => 'required|after:start_time'
        ]);

        $hour = Hour::find($request->id);
        $event = $request->event;

        $startDate = new Carbon($request->date);
        $startTime = $startDate->setTimeFromTimeString($request->start_time);

        $endDate = new Carbon($request->date);
        $endTime = $endDate->setTimeFromTimeString($request->end_time);

        $hour->needs_review = false;
        $hour->event_id = $event;
        $hour->start_time = $startTime;
        $hour->end_time = $endTime;
        try {
            $hour->saveOrFail();
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'success']);
    }
}
