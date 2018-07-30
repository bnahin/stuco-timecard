<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHoursRequest;
use Illuminate\Http\Request;

class HoursController extends Controller
{
    /**
     * Store new hour submission
     * "Add New Activity"
     *
     * @param \App\Http\Requests\StoreHoursRequest $request
     *
     * @return void
     */
    public function store(StoreHoursRequest $request)
    {
        return $request->validated();
    }
}
