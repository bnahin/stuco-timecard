<?php
/**
 *
 * @author Blake Nahin <blake@zseartcc.org>
 */

namespace App\Helpers;


use App\BlockedUser;
use App\Hour;
use App\StudentInfo;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class HourHelper
{

    /**
     * Check if user is clocked out
     * @param int $stuid
     *
     * @return bool
     */
    public static function getClockData($stuid)
    {
        return Hour::where('student_id', $stuid)
            ->whereNotNull('start_time')
            ->whereNull('end_time')
            ->exists();
    }
}