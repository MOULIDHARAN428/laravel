<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Auth::shouldUse('api');
        $user = Auth::user();
        if (!isset($user)){
            return response('Log in please.', 403);
        }
        $user = User::where('email', $user->email)->first();
        if (!isset($user)){
            return response('User is Not admin.', 403);
        }
        if ($user->isAdmin == 1) {
            return $next($request);
        }

        // Redirect or respond as needed if not an admin
        return response('Unauthorized. Admin access required.', 403);
    }
}
