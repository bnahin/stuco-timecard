<?php

namespace App\Http\Controllers;

use App\BlockedUser;
use App\StudentInfo;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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

            return response()->json(['status' => 'success']);
        }

        //No students exist
        return response()->json(['status' => 'error', 'message' => 'No students in club.']);
    }
}
