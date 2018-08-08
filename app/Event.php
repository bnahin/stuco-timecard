<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Event
 *
 * @property int                 $id
 * @property string              $event_name
 * @property int                 $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event active()
 */
class Event extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function hours()
    {
        $this->hasMany(Hour::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
