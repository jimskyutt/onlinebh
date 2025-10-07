<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function index()
    {
        return view('account.index', [
            'user' => auth()->user()
        ]);
    }

    public function updateUsername(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->user_id . ',user_id'],
        ]);

        $user->username = $validated['username'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Username updated successfully!',
            'new_username' => $user->username
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
}
