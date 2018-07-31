<?php

namespace App\Http\Controllers;

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
        return $this->excel->load('storage/app/public/students-2019.xlsx')->get();
    }
}
