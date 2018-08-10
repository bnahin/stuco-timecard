<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    /**
     * Eloquent Relationships
     */

    public function hours()
    {
        return $this->hasMany(Hour::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
