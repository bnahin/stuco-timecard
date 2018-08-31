<?php

namespace App\Http\Controllers;

use App\Hour;
use App\Http\Requests\GetStudentRequest;
use App\StudentInfo;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getInfo(GetStudentRequest $request)
    {
        $return = ['status' => 'success'];

        $id = $request->id;
        if ($hour = Hour::getClockData($id)) {
            $return['currentHour'] = $hour;
        }

        $student = StudentInfo::where('student_id', $id)->first();
        $name = $student->full_name;
        $grade = $student->grade;
        $return['user']['name'] = $name;
        $return['user']['grade'] = $grade;

        return response()->json($return);
    }
}
