<?php

namespace App\Http\Controllers;

use App\StudentInfo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function index()
    {
        return view('pages.admin');
    }

    public function processAssignStudentsTable(Request $request)
    {
        $students = StudentInfo::select(['student_id', 'first_name', 'last_name', 'grade', 'email'])->get();

        return DataTables::of($students)->make();
    }
}
