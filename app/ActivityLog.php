<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at'];

    public static function new($message) {
        static::insert([
            'user_id' => Auth::user()->id,
            'message' => $message,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
