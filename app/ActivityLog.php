<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * App\ActivityLog
 *
 * @property int                 $id
 * @property int                 $user_id
 * @property int                 $club_id
 * @property string              $message
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Club      $club
 * @property-read \App\User      $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityLog whereClubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityLog whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $admin_id
 * @property-read \App\Admin|null $admin
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ActivityLog whereAdminId($value)
 */
class ActivityLog extends Model
{
    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function (Builder $builder) {
            $builder->where('club_id', getClubId());
        });
    }

    public static function new($message)
    {
        $auth_field = (isAdmin()) ? 'admin_id' : 'user_id';
        static::insert([
            $auth_field  => Auth::user()->id,
            'message'    => $message,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'club_id'    => (app()->isLocal()) ? 1 : Session::get('club-id')
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);

    }
}
