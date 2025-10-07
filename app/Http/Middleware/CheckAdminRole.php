<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
{
    \Log::info('CheckAdminRole - Auth Check', [
        'is_authenticated' => Auth::check(),
        'user' => Auth::user(),
        'session_id' => session()->getId()
    ]);

    if (Auth::check() && Auth::user()->role === 'admin') {
        return $next($request);
    }

    \Log::warning('Unauthorized access attempt', [
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);
    
    return redirect('/')->with('error', 'Unauthorized access.');
}
}
