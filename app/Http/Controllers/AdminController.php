<?php

namespace App\Http\Controllers;

use App\StudentInfo;
use App\User;
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
        $data = null;
        switch ($page) {
            case 'assign':
                $data = $this->getAssignedStudents();
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
}
