<?php
/**
 *
 * @author Blake Nahin <blake@zseartcc.org>
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthHelper
{
    public static function isLoggedIn()
    {
        return Auth::check();
    }

    public static function isUser()
    {
        return Auth::guard('user')->check();
    }

    public static function isAdmin()
    {
        return Auth::guard('admin')->check();
    }

    public static function getClubId()
    {
        return Session::get('club-id');
    }

    public static function logout()
    {
        Auth::logout();

        Session::invalidate();

        Session::regenerate();
    }
}