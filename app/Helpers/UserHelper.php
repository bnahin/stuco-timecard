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
        if (!$student->exists() || !$student->first()->user) {
            $isBlocked = false;

            return false;
        }
        $belongs = $student->first()->user()->exists() &&
            $student->first()->user->clubs()->exists()
            && $student->first()->user->clubs()
                ->where('club_id', getClubId())->exists();

        $isBlocked = BlockedUser::where([
            'club_id' => getClubId(),
            'user_id' => $student->first()->user->id
        ])->exists();

        return $belongs;
    }

    /**
     * Get User Object from Student ID
     * @param int $stuid Student ID
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|null
     */
    public static function getUserFromStudentId($stuid)
    {
        $studentInfo = StudentInfo::where('student_id', $stuid);

        return ($studentInfo->exists()) ? $studentInfo->user() : null;
    }
}