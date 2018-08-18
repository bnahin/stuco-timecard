<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BlockedUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $club_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Club $club
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedUser whereClubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedUser whereUserId($value)
 * @mixin \Eloquent
 */
class BlockedUser extends Model
{
    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at'];

    public function club() {
        return $this->belongsTo(Club::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
