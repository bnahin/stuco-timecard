<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Announcement
 *
 * @property int $id
 * @property int $club_id
 * @property string $post_title
 * @property string $post_body
 * @property string $admin_id
 * @property int $email_sent
 * @property int $is_global
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Admin $admin
 * @property-read \App\Club $club
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement recent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereClubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereEmailSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereIsGlobal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement wherePostBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement wherePostTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Announcement extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            if (getClubId()) {
                $builder->where('club_id', getClubId());
                $builder->orWhere('is_global', true);
            }

            $builder->orderBy('created_at', 'desc');
        });
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function scopeRecent(Builder $builder)
    {
        return $builder->where('updated_at', '>=', Carbon::now()->subWeeks(2));
    }
}
