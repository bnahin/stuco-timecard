<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Hour
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string $comments
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Event $event
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hour whereUserId($value)
 * @mixin \Eloquent
 */
class Hour extends Model
{
    protected $guarded = [];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
