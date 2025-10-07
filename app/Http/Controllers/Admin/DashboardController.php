<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardingHouse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $boardingHouses = BoardingHouse::select('id', 'name', 'latitude', 'longitude', 'status')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('admin.dashboard', compact('boardingHouses'));
    }

    public function getMapData()
    {
        try {
            $boardingHouses = BoardingHouse::select(
                    'id', 
                    'name', 
                    'latitude', 
                    'longitude', 
                    'status',
                    'contact_person',
                    'contact_number',
                    'current_boarders',
                    'max_boarders',
                    'available_rooms',
                    'max_rooms',
                    'price',
                    'bed_space_price',
                    'room_space_price',
                    'space_type',
                    'address',
                    'bh_image'
                )
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['images' => function($query) {
                    $query->where('is_primary', true);
                }])
                ->get();

            $data = [];
            foreach ($boardingHouses as $bh) {
                try {
                    \Log::info('Processing boarding house:', [
                        'id' => $bh->id,
                        'name' => $bh->name,
                        'space_type' => $bh->space_type,
                        'available_rooms' => $bh->available_rooms,
                        'max_rooms' => $bh->max_rooms,
                        'has_image' => !empty($bh->bh_image)
                    ]);

                    if (!view()->exists('admin.components.bh-popup')) {
                        throw new \Exception('View not found: admin.components.bh-popup');
                    }

                    $testView = view('admin.components.bh-popup', ['bh' => $bh])->render();
                    $popupView = $testView;
                } catch (\Exception $e) {
                    \Log::error('Error rendering popup for BH ' . $bh->id, [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $popupView = '<div class="p-2">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }

                $data[] = [
                    'id' => $bh->id,
                    'name' => $bh->name,
                    'lat' => (float)$bh->latitude,
                    'lng' => (float)$bh->longitude,
                    'status' => $bh->status,
                    'popup' => $popupView
                ];
            }

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Critical error in getMapData', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load map data',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}