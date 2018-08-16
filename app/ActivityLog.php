<?php

namespace App;

use Carbon\Carbon;
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
 */
class ActivityLog extends Model
{
    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at'];

    public static function new($message)
    {
        static::insert([
            'user_id'    => Auth::user()->id,
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

    public function club()
    {
        return $this->belongsTo(Club::class);

    }
}
