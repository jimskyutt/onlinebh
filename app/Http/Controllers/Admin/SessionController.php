<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function updateSession()
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'inactive', 'message' => 'User not authenticated'], 401);
        }

        $user = Auth::user();
        
        try {
            // Update or create session record
            UserSession::updateOrCreate(
                ['user_id' => $user->user_id], // Use user_id instead of id
                [
                    'session_id' => session()->getId(),
                    'last_activity' => now()
                ]
            );
            
            return response()->json([
                'status' => 'active',
                'user_id' => $user->user_id,
                'username' => $user->username
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating user session', [
                'user_id' => $user->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update session',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function checkActiveSessions()
    {
        if (!Auth::check()) {
            return response()->json(['active_sessions' => 0]);
        }
        
        try {
            $activeSessions = UserSession::where('user_id', '!=', Auth::user()->user_id)
                ->where('last_activity', '>', now()->subMinutes(config('auth.guards.web.lifetime', 120)))
                ->count();
                
            return response()->json([
                'active_sessions' => $activeSessions,
                'current_user_id' => Auth::user()->user_id
            ]);
                
        } catch (\Exception $e) {
            \Log::error('Error checking active sessions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check active sessions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display a listing of active user sessions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $sessions = UserSession::with(['user.boardingHouses'])
                ->where('last_activity', '>', now()->subMinutes(config('auth.guards.web.lifetime', 120)))
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    $session->last_activity_diff = Carbon::parse($session->last_activity)->diffForHumans();
                    
                    // Add user information if available
                    if ($session->user) {
                        $session->user->makeVisible(['user_id', 'username', 'role', 'created_at', 'updated_at']);
                    }
                    
                    return $session;
                });
                
            return view('admin.sessions.index', [
                'sessions' => $sessions,
                'current_time' => now()->toDateTimeString()
            ]);
                
        } catch (\Exception $e) {
            \Log::error('Error loading sessions page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return an empty sessions collection if there's an error
            return view('admin.sessions.index', [
                'sessions' => collect(),
                'error' => 'Failed to load sessions. Please check the logs for more details.'
            ]);
        }
    }
}
