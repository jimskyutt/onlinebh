<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckOwnerRole
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
        Log::info('CheckOwnerRole - Auth Check', [
            'is_authenticated' => Auth::check(),
            'user' => Auth::user(),
            'session_id' => session()->getId()
        ]);

        if (Auth::check() && Auth::user()->role === 'owner') {
            return $next($request);
        }

        Log::warning('Unauthorized access attempt - Owner role required', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_role' => Auth::check() ? Auth::user()->role : 'guest'
        ]);
        
        return redirect('/')->with('error', 'Unauthorized access. Owner privileges required.');
    }
}
