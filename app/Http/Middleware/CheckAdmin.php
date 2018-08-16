<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('admin')->check()
            || !Auth::user()->clubs()->where('clubs.id', getClubId())->exists()) {
            return redirect('home')->with('forbidden', true);
        }

        return $next($request);
    }
}
