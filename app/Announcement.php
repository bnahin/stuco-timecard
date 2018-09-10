<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
