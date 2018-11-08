<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Club
 *
 * @property int                                                              $id
 * @property string                                                           $join_code
 * @property string                                                           $club_name
 * @property int                                                              $public
 * @property \Carbon\Carbon|null                                              $created_at
 * @property \Carbon\Carbon|null                                              $updated_at
 * @property string|null                                                      $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[]       $events
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Hour[]        $hours
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ActivityLog[] $logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[]        $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereClubName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereJoinCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Club whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Admin[]       $admins
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BlockedUser[] $blocks
 * @property-read \App\Setting $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Announcement[] $announcements
 */
class Club extends Model
{
    /**
     * Eloquent Relationships
     */

    public function hours()
    {
        return $this->hasMany(Hour::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class)
            ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function blocks()
    {
        return $this->hasMany(BlockedUser::class);
    }

    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    public function announcements() {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Completely destroy club
     * @throws \Exception
     * @return boolean
     */
    public function fullDestroy() {
        //Purge Hours
        $this->hours()->delete();

        //Detach Users
        $this->users()->detach();

        //Delete Blocked Users
        $this->blocks()->delete();

        //Detach Admins
        $this->admins()->detach();

        //Delete Events
        $this->events()->forceDelete();

        //Delete Announcements
        $this->announcements()->delete();

        //Delete Teachers
        //WIP

        //Delete Activity Logs
        $this->logs()->delete();

        //Delete Settings
        $this->settings()->delete();

        //Delete Club itself!
        $this->delete();

        return true;
    }
}
