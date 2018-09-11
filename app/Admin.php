<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Admin
 *
 * @property int
 *               $id
 * @property string
 *               $google_id
 * @property string
 *               $first_name
 * @property string
 *               $last_name
 * @property string
 *               $email
 * @property string|null
 *               $remember_token
 * @property \Carbon\Carbon|null
 *               $created_at
 * @property \Carbon\Carbon|null
 *               $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Club[]
 *                    $clubs
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[]
 *                $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Admin whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ActivityLog[] $logs
 */
class Admin extends Authenticatable
{
    use Notifiable;

    public function clubs()
    {
        return $this->belongsToMany(Club::class)
            ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Eloquent Mutators
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setFirstNameAttribute($val)
    {
        $this->attributes['first_name'] = ucwords($val);
    }

    public function setLastNameAttribute($val)
    {
        $this->attributes['last_name'] = ucwords($val);
    }

    public function posts() {
        return $this->hasMany(Announcement::class);
    }
}
