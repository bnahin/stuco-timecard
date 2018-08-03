<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property int
 *               $id
 * @property string
 *               $name
 * @property string
 *               $email
 * @property string
 *               $password
 * @property string|null
 *               $remember_token
 * @property \Carbon\Carbon|null
 *               $created_at
 * @property \Carbon\Carbon|null
 *               $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[]
 *                $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string                                                    $google_id
 * @property int|null                                                  $student_id
 * @property string                                                    $first_name
 * @property string                                                    $last_name
 * @property string                                                    $domain
 * @property-read mixed                                                $full_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereStudentId($value)
 * @property int                                                       $is_admin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Hour[] $hours
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIsAdmin($value)
 * @property string                                                    $grade
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGrade($value)
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\StudentInfo $student
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * Eloquent Relationships
     */
    public function hours()
    {
        return $this->hasMany(Hour::class);
    }
    public function student() {
        return $this->hasOne(StudentInfo::class);
    }

    /**
     * Functions
     */
    public function isAdmin()
    {
        return $this->is_admin == 1;
    }
}
