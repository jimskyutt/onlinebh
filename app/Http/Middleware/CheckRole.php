<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        Log::info('CheckRole - Auth Check', [
            'is_authenticated' => Auth::check(),
            'user' => Auth::user(),
            'required_roles' => $roles,
            'session_id' => session()->getId()
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Check if user has any of the required roles
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        Log::warning('Unauthorized access attempt', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_role' => Auth::user()->role,
            'required_roles' => $roles
        ]);

        return redirect('/')->with('error', 'You do not have permission to access this page.');
    }
}
