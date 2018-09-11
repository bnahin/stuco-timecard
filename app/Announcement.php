<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
