<?php
/**
 *
 * @author Blake Nahin <blake@zseartcc.org>
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Auth;

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
}