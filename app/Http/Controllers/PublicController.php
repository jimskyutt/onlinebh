<?php

namespace App\Http\Controllers;

use App\Models\BoardingHouse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $boardingHousesCount = BoardingHouse::where('status', 'Available')->count();
        $featuredHouses = BoardingHouse::with('facilities')
            ->where('status', 'Available')
            ->inRandomOrder()
            ->take(6)
            ->get();

        return view('index', compact('boardingHousesCount', 'featuredHouses'));
    }

    public function getMapData()
    {
        $boardingHouses = BoardingHouse::with(['facilities', 'images'])->get();
        
        $data = $boardingHouses->map(function($house) {
            // Ensure images are loaded as a collection
            $house->setRelation('images', $house->images ?? collect());
            
            return [
                'id' => $house->id,
                'name' => $house->name,
                'lat' => (float)$house->latitude,
                'lng' => (float)$house->longitude,
                'status' => $house->status,
                'address' => $house->address,
                'price' => number_format($house->price, 2),
                'contact_person' => $house->contact_person,
                'contact_number' => $house->contact_number,
                'current_boarders' => $house->current_boarders,
                'max_boarders' => $house->max_boarders,
                'bh_image' => $house->bh_image,
                'images' => $house->images->map(function($image) {
                    return [
                        'image_path' => $image->image_path,
                        'is_primary' => $image->is_primary
                    ];
                })->toArray(),
                'popup' => view('partials.map-popup', ['house' => $house])->render()
            ];
        });

        return response()->json($data);
    }
    
    public function show(BoardingHouse $boardingHouse)
    {
        $boardingHouse->load(['facilities', 'images' => function($query) {
            $query->orderBy('is_primary', 'desc')->orderBy('display_order');
        }]);
        
        $relatedHouses = BoardingHouse::where('id', '!=', $boardingHouse->id)
            ->where('status', 'Available')
            ->inRandomOrder()
            ->take(3)
            ->with(['images' => function($query) {
                $query->where('is_primary', true);
            }])
            ->get();
            
        return view('public.boarding-houses.show', [
            'house' => $boardingHouse,
            'relatedHouses' => $relatedHouses
        ]);
    }
}
