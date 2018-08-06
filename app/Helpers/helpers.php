<?php
/**
 * Function Helpers
 * @author Blake Nahin <blake@zseartcc.org>
 */

if (!function_exists('log_action')) {
    function log_action($message)
    {
        return App\ActivityLog::new($message);
    }
}