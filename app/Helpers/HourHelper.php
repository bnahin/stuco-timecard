<?php
/**
 *
 * @author Blake Nahin <blake@zseartcc.org>
 */

namespace App\Helpers;


use App\BlockedUser;
use App\StudentInfo;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class UserHelper
{

    /**
     * User belongs to current club
     *
     * @param \Illuminate\Database\Eloquent\Builder $student
     *
     * @param  bool                                 $isBlocked
     *
     * @return bool
     */
    public static function belongsToClub(Builder $student, &$isBlocked)
    {
        $belongs = $student->exists() &&
            $student->first()->user()->exists() &&
            $student->first()->user->clubs()->exists()
            && $student->first()->user->clubs()
                ->where('club_id', getClubId())->exists();
        $isBlocked = ($student->exists()) ?
            BlockedUser::where([
                'club_id' => getClubId(),
                'user_id' => $student->first()->user->id
            ])->exists() : false;

        return $belongs;
    }
}