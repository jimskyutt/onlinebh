<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use Closure;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            try {
                $sessionId = session()->getId();
                $user = Auth::user();
                $userId = $user->getAuthIdentifier();
                
                // Ensure user_id is numeric
                if (!is_numeric($userId)) {
                    $userId = $user->user_id ?? null;
                    if (!$userId) {
                        throw new \Exception('Invalid user ID format: ' . gettype($userId) . ' - ' . json_encode($userId));
                    }
                }
                
                $userId = (int)$userId; // Ensure it's an integer
                
                \Log::info('Updating user session', [
                    'user_id' => $userId,
                    'user_type' => gettype($userId),
                    'session_id' => $sessionId,
                    'time' => now()
                ]);
                
                // First, try to find an existing session for this user
                $session = UserSession::where('user_id', $userId)
                    ->orWhere('session_id', $sessionId)
                    ->first();
                
                if ($session) {
                    // Update existing session
                    $session->update([
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'last_activity' => now()
                    ]);
                } else {
                    // Create new session
                    $session = UserSession::create([
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'last_activity' => now()
                    ]);
                }
                
                \Log::info('Session updated/created', [
                    'session_id' => $session->id,
                    'user_id' => $session->user_id,
                    'session_data' => $session->toArray()
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Error in TrackUserActivity', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user' => [
                        'id' => Auth::id(),
                        'type' => gettype(Auth::id()),
                        'class' => get_class(Auth::user())
                    ]
                ]);
            }
        } else {
            \Log::info('User not authenticated in TrackUserActivity', [
                'session_id' => session()->getId(),
                'ip' => $request->ip()
            ]);
        }

        return $next($request);
    }
}
