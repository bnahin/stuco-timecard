<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Setting
 *
 * @property int $club_id
 * @property string|null $club_desc
 * @property int $allow_mark
 * @property int $allow_delete
 * @property int $allow_comments
 * @property int $master
 * @property-read \App\Club $club
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereAllowComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereAllowDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereAllowMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereClubDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereClubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereMaster($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    public $timestamps = false;
    public $primaryKey = "club_id";

    public function club() {
        return $this->belongsTo(Club::class);
    }
}
