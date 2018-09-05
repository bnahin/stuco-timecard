<?php

namespace App\Policies;

use App\User;
use App\Hour;
use Illuminate\Auth\Access\HandlesAuthorization;

class HourPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view hour history.
     *
     * @param  \App\User $user
     * @param  \App\Hour $hour
     *
     * @return mixed
     */
    public function view(User $user, Hour $hour)
    {
        //Handled by controller
        return $user->isAdmin() || $user->id === $hour->user_id;
    }

    /**
     * Determine whether the user can create hours.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return isAdmin();
    }

    /**
     * Determine whether the user can update the hour.
     *
     * @param  \App\User|\App\Admin $user
     * @param  \App\Hour $hour
     *
     * @return mixed
     */
    public function update($user, Hour $hour)
    {
        //Admin update: handled by middleware
        //Clock in & Mark here
        return isAdmin() || $hour->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the hour.
     *
     * @param  \App\User|\App\Admin $user
     * @param  \App\Hour $hour
     *
     * @return mixed
     */
    public function delete($user, Hour $hour)
    {
        return (!$hour->end_time)
            ? $hour->user_id === $user->id : isAdmin() === true;
    }

    /**
     * Determine whether the user can restore the hour.
     *
     * @param  \App\User $user
     * @param  \App\Hour $hour
     *
     * @return mixed
     */
    public function restore(User $user, Hour $hour)
    {
        // Handled by admin middleware
    }

    /**
     * Determine whether the user can permanently delete the hour.
     *
     * @param  \App\User $user
     * @param  \App\Hour $hour
     *
     * @return mixed
     */
    public function forceDelete(User $user, Hour $hour)
    {
        // Handled by admin middleware
    }
}
