<?php

namespace App\Http\Controllers;

use App\StudentInfo;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\Storage;

class StudentInfoController extends Controller
{
    protected $excel;

    private $testPath;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
        $this->testPath = Storage::disk('local')->url('public/students-2019.xlsx');
    }

    public function handleImport()
    {
        ini_set('max_execution_time', 120);
        $students = $this->excel->load('storage/app/public/students-2019.xlsx')->get();

        $found = array();
        $errors = array();
        $count = 0;

        StudentInfo::truncate();
        foreach ($students as $student) {
            if (!$student->grade || !$student->student_id || !$student->first_name || !$student->last_name) {
                //Invalid data, not complete
                $errors[] = $student;
                continue;
            }

            $info = new StudentInfo;
            $info->grade = $student->grade;
            $info->student_id = $student->student_id;
            $info->first_name = $student->first_name;
            $info->last_name = $student->last_name;
            if (!$student->stuemail) {
                // Fallback in case email does not exist somehow
                $info->email = $student->student_id . "@ecrchs.org";
            } else {
                $info->email = $student->stuemail;
            }

            $user = User::where('email', $student->stuemail)->with('student');
            if ($user->exists()) {
                //Associate user
                $user = $user->first();
                $user->student()->save($info);
                $found[] = $user;
            } else {
                //No user association
                $info->save();
            }
            $count++;
        }

        return response()->json(["Import complete with $count students and " . count($errors) . " errors.\n"
            . count($found) . "had user model(s)."]);
    }
}
