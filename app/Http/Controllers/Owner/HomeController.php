<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoardingHouse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function index()
    {
        $user = Auth::user();
        $boardingHouses = BoardingHouse::where('user_id', $user->user_id)
            ->latest()
            ->paginate(12);

        return view('owner.home', compact('boardingHouses'));
    }
}
