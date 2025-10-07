<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        Log::info('Login attempt', [
            'username' => $credentials['username'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'time' => now()->toDateTimeString()
        ]);

        $user = User::where('username', $credentials['username'])->first();
        
        if ($user) {
            $passwordMatch = \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password);
            
            Log::info('User found', [
                'user_id' => $user->user_id,
                'username' => $user->username,
                'password_match' => $passwordMatch,
                'role' => $user->role
            ]);

            if ($passwordMatch) {
                Auth::login($user, $request->filled('remember'));
                $request->session()->regenerate();
                
                Log::info('Login successful', [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'role' => $user->role,
                    'intended' => $request->session()->get('url.intended')
                ]);
                
                if ($user->role === 'admin') {
                    return redirect()->intended(route('admin.dashboard'))
                        ->with('status', 'Welcome back, Admin!');
                } elseif ($user->role === 'owner') {
                    return redirect()->intended(route('owner.home'))
                        ->with('status', 'Welcome back, Owner!');
                }
                
                return redirect()->intended('/');
            }
        } else {
            Log::warning('User not found', ['username' => $credentials['username']]);
        }

        Log::warning('Login failed', [
            'username' => $credentials['username'],
            'error' => 'Invalid credentials'
        ]);

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->session()->getId();
        
        // Update the user's session record with logout time
        if ($user) {
            \App\Models\UserSession::where('user_id', $user->user_id)
                ->where('session_id', $sessionId)
                ->update([
                    'logged_out_at' => now(),
                    'last_activity' => now() // Update last activity to match logout time
                ]);
        }
        
        Log::info('User logged out', [
            'user_id' => $user->user_id ?? null,
            'username' => $user->username ?? null,
            'session_id' => $sessionId
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}