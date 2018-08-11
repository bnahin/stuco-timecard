<?php

namespace App\Http\Controllers;

use App\Common\Bnahin\EcrchsAuth;
use App\StudentInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class StudentInfoController extends Controller
{
    protected $excel;

    private $testPath;

    private $auth;

    public function __construct(Excel $excel, EcrchsAuth $auth)
    {
        $this->excel = $excel;
        $this->auth = $auth;

        $this->testPath = Storage::disk('local')->url('public/students-2019.xlsx');
    }

    public function handleImport()
    {
        $this->auth->importEnrolled();
    }
}
