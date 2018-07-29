<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HoursController extends Controller
{
    /**
     * Store new hour submission
     * "Add New Activity"
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        return dd($request->id);
    }
}
