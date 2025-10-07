<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BoardingHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BoardingHouseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function index()
    {
        $boardingHouses = Auth::user()->boardingHouses()->latest()->paginate(10);
        return view('owner.boarding-houses.index', compact('boardingHouses'));
    }

    public function create()
    {
        return view('owner.boarding-houses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price' => 'required|numeric|min:0',
            'max_boarders' => 'required|integer|min:1',
            'current_boarders' => 'required|integer|min:0',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'status' => 'required|in:Available,Under Maintenance,Full',
            'amenities' => 'nullable|string',
            'bh_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('bh_image')) {
            $imagePath = $request->file('bh_image')->store('boarding_houses', 'public');
            $validated['bh_image'] = $imagePath;
        }

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        BoardingHouse::create($validated);

        return redirect()->route('owner.home')
            ->with('success', 'Boarding house created successfully.');
    }

    public function edit(BoardingHouse $boardingHouse)
    {
        $this->authorize('update', $boardingHouse);
        return view('owner.boarding-houses.edit', compact('boardingHouse'));
    }

    public function update(Request $request, BoardingHouse $boardingHouse)
    {
        $this->authorize('update', $boardingHouse);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'price' => 'required|numeric|min:0',
            'max_boarders' => 'required|integer|min:1',
            'current_boarders' => 'required|integer|min:0',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'status' => 'required|in:Available,Under Maintenance,Full',
            'amenities' => 'nullable|string',
            'bh_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('bh_image')) {
            if ($boardingHouse->bh_image) {
                Storage::disk('public')->delete($boardingHouse->bh_image);
            }
            $imagePath = $request->file('bh_image')->store('boarding_houses', 'public');
            $validated['bh_image'] = $imagePath;
        }

        $boardingHouse->update($validated);

        return redirect()->route('owner.home')
            ->with('success', 'Boarding house updated successfully.');
    }

    public function show(BoardingHouse $boardingHouse)
    {
        $this->authorize('view', $boardingHouse);
        
        $backUrl = url()->previous() === route('owner.home') ? route('owner.home') : route('owner.boarding-houses.index');
        
        return view('owner.boarding-houses.show', [
            'boardingHouse' => $boardingHouse->load('facilities'),
            'backUrl' => $backUrl
        ]);
    }

    public function destroy(BoardingHouse $boardingHouse)
    {
        $this->authorize('delete', $boardingHouse);

        if ($boardingHouse->bh_image) {
            Storage::disk('public')->delete($boardingHouse->bh_image);
        }

        $boardingHouse->delete();

        return redirect()->route('owner.home')
            ->with('success', 'Boarding house deleted successfully.');
    }
}
