<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Club
 *
 * @property int $id
 * @property string $join_code
 * @property string $club_name
 * @property int $public
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Hour[] $hours
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ActivityLog[] $logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereClubName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereJoinCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Admin[] $admins
 */
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

    public function admins() {
        return $this->belongsToMany(Admin::class)
            ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
