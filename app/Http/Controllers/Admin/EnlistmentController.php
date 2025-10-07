<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facility;
use App\Models\BoardingHouse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EnlistmentController extends Controller
{
    public function index()
    {
        $boardingHouses = BoardingHouse::with(['facilities', 'images'])
            ->latest()
            ->paginate(12);
    
        return view('admin.enlistments.index', compact('boardingHouses'));
    }
 
    public function create(Request $request)
    {
        $user = auth()->user();
        $from = $request->query('from');
        
        $backUrl = $user->role === 'admin' 
            ? route('admin.enlistments')
            : route('owner.home');
            
        if ($from === 'dashboard') {
            $backUrl = route('admin.dashboard');
        } elseif ($from === 'owner-home') {
            $backUrl = route('owner.home');
        }
        
        $facilities = Facility::all();
        return view('admin.enlistments.create', [
            'facilities' => $facilities,
            'backUrl' => $backUrl
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'space_type' => 'required|in:Bed Space,Room Space,Both',
            'status' => 'required|in:Available,Under Maintenance,Full',
            'bh_images' => 'required|array|min:1|max:5',
            'bh_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'bed_space_price' => 'required_if:space_type,Bed Space,Both|nullable|numeric|min:0',
            'bed_space_per_room' => 'required_if:space_type,Bed Space|nullable|integer|min:1',
            'room_space_price' => 'required_if:space_type,Room Space,Both|nullable|numeric|min:0',
            'max_boarders' => 'required_if:space_type,Bed Space,Both|nullable|integer|min:1',
            'current_boarders' => 'nullable|integer|min:0|lte:max_boarders',
            'max_rooms' => 'required_if:space_type,Room Space,Both|nullable|integer|min:1',
            'available_rooms' => 'nullable|integer|min:0|lte:max_rooms',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string',
            'location' => 'required|string',
            'facilities' => 'required|array|min:1',
            'facilities.*' => 'exists:facilities,facility_id',
            'amenities' => 'nullable|string'
        ]);

        try {
            // Check if any images were uploaded
            if (!$request->hasFile('bh_images')) {
                return back()->with('error', 'Please upload at least one image.')->withInput();
            }

            $images = [];
            
            // Process each uploaded image
            foreach ($request->file('bh_images') as $key => $image) {
                $imageName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('boarding-houses', $imageName, 'public');
                $images[] = [
                    'image_path' => $imagePath,
                    'is_primary' => $key === 0, // First image is primary
                    'display_order' => $key + 1, // Start display order from 1
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            $location = explode(',', $request->location);
            $latitude = trim($location[0]);
            $longitude = trim($location[1]);

            $existingBoardingHouse = \App\Models\BoardingHouse::where('contact_person', $validated['contact_person'])
                ->first();
            
            if ($existingBoardingHouse) {
                $user = \App\Models\User::find($existingBoardingHouse->user_id);
            } else {
                $firstWord = strtolower(explode(' ', $validated['contact_person'])[0]) . rand(1000, 9999);
                $password = $firstWord . rand(1000, 9999);
                
                $user = \App\Models\User::create([
                    'username' => $firstWord,
                    'password' => bcrypt($password),
                    'role' => 'owner',
                ]);
                
                session([
                    'username' => $firstWord,
                    'password' => $password,
                    'show_credentials' => true
                ]);
            }

            $boardingHouse = new BoardingHouse([
                'user_id' => $user->user_id,
                'name' => $validated['name'],
                'space_type' => $validated['space_type'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'price' => $validated['bed_space_price'] ?? $validated['room_space_price'] ?? 0, // Keep for backward compatibility
                'bed_space_price' => $validated['bed_space_price'] ?? null,
'bed_space_per_room' => $validated['space_type'] === 'Bed Space' ? (isset($validated['bed_space_per_room']) ? (int)$validated['bed_space_per_room'] : 1) : 1,
                'room_space_price' => $validated['room_space_price'] ?? null,
                'max_boarders' => in_array($validated['space_type'], ['Bed Space', 'Both']) ? $validated['max_boarders'] : null,
                'current_boarders' => in_array($validated['space_type'], ['Bed Space', 'Both']) ? ($validated['current_boarders'] ?? 0) : 0,
                'max_rooms' => in_array($validated['space_type'], ['Room Space', 'Both']) ? $validated['max_rooms'] : null,
                'available_rooms' => in_array($validated['space_type'], ['Room Space', 'Both']) ? ($validated['available_rooms'] ?? $validated['max_rooms'] ?? 1) : null,
                'contact_person' => $validated['contact_person'],
                'contact_number' => $validated['contact_number'],
                'address' => $validated['address'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'amenities' => $validated['amenities'] ?? null,
            ]);

            $boardingHouse->save();

            // Save images
            $boardingHouse->images()->createMany($images);

            // Attach facilities if any are selected
            if ($request->has('facilities') && is_array($request->facilities)) {
                $boardingHouse->facilities()->sync($request->facilities);
            }

            $redirect = redirect()->route('admin.enlistments.create')
            ->with('success', 'Boarding house added successfully!');
        
            if (!isset($existingBoardingHouse)) {
                $redirect = $redirect->with([
                    'show_credentials' => true,
                    'username' => $firstWord,
                    'password' => $password
                ]);
            }

            return $redirect;

        } catch (\Exception $e) {

            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return back()->with('error', 'Error adding boarding house: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function destroy(BoardingHouse $boardingHouse)
    {
        try {
            $userId = $boardingHouse->user_id;
        
            $otherBoardingHouses = BoardingHouse::where('user_id', $userId)
                ->where('id', '!=', $boardingHouse->id)
                ->count();

            $boardingHouse->delete();

            if ($otherBoardingHouses === 0) {
                $user = User::find($userId);
                if ($user) {
                    $user->delete();
                }
            }

            return redirect()->route('admin.enlistments')
                ->with('success', 'Boarding house deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting boarding house: ' . $e->getMessage());
            return back()->with('error', 'Error deleting boarding house: ' . $e->getMessage());
        }
    }

    public function show(BoardingHouse $boardingHouse, Request $request)
    {
        $user = auth()->user();
        
        \Log::info('Show boarding house access check', [
            'user_id' => $user->user_id,
            'user_role' => $user->role,
            'boarding_house_user_id' => $boardingHouse->user_id,
            'boarding_house_id' => $boardingHouse->id
        ]);

        if ($user->role === 'owner' && (int)$boardingHouse->user_id !== (int)$user->user_id) {
            \Log::warning('Unauthorized access attempt to boarding house', [
                'user_id' => $user->user_id,
                'boarding_house_user_id' => $boardingHouse->user_id
            ]);
            abort(403, 'Unauthorized action.');
        }

        $boardingHouse->load('facilities');
        $from = $request->query('from');
        
        $backUrl = $user->role === 'admin' 
            ? ($from === 'map' ? route('admin.dashboard') : route('admin.enlistments'))
            : route('owner.home');
        
        return view('admin.enlistments.show', [
            'boardingHouse' => $boardingHouse,
            'backUrl' => $backUrl
        ]);
    }

    public function edit(BoardingHouse $boardingHouse)
    {
        $user = auth()->user();
        
        if ($user->role === 'owner' && (int)$boardingHouse->user_id !== (int)$user->user_id) {
            \Log::warning('Unauthorized edit attempt', [
                'user_id' => $user->user_id,
                'boarding_house_user_id' => $boardingHouse->user_id
            ]);
            abort(403, 'Unauthorized action.');
        }

        $facilities = Facility::all();
        $boardingHouse->load('facilities');
        return view('admin.enlistments.edit', compact('boardingHouse', 'facilities'));
    }

    public function update(Request $request, BoardingHouse $boardingHouse)
    {
        $user = auth()->user();
        
        if ($user->role === 'owner' && (int)$boardingHouse->user_id !== (int)$user->user_id) {
            \Log::warning('Unauthorized update attempt', [
                'user_id' => $user->user_id,
                'boarding_house_user_id' => $boardingHouse->user_id
            ]);
            abort(403, 'Unauthorized action.');
        }
        
        // Log the request data
        \Log::info('Update request data', [
            'has_files' => $request->hasFile('bh_images'),
            'files_count' => $request->hasFile('bh_images') ? count($request->file('bh_images')) : 0,
            'primary_image' => $request->input('primary_image'),
            'primary_new_image' => $request->input('primary_new_image'),
            'removed_images' => $request->input('removed_images', []),
            'all_input' => $request->except(['_token', '_method', 'new_images'])
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'space_type' => 'required|in:Bed Space,Room Space,Both',
            'description' => 'required|string',
            'bed_space_price' => 'required_if:space_type,Bed Space,Both|nullable|numeric|min:0',
            'bed_space_per_room' => 'required_if:space_type,Bed Space,Both|nullable|integer|min:1',
            'room_space_price' => 'required_if:space_type,Room Space,Both|nullable|numeric|min:0',
            'max_boarders' => 'required_if:space_type,Bed Space,Both|nullable|integer|min:1',
            'current_boarders' => 'nullable|integer|min:0|lte:max_boarders',
            'max_rooms' => 'required_if:space_type,Room Space,Both|nullable|integer|min:1',
            'available_rooms' => 'nullable|integer|min:0|lte:max_rooms',
            'address' => 'required|string|max:255',
            'location' => 'required|string',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'status' => 'required|in:Available,Full,Under Maintenance',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'exists:boarding_house_images,id',
            'bh_images' => 'nullable|array',
            'bh_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'removed_images' => 'nullable|array',
            'removed_images.*' => 'exists:boarding_house_images,id',
            'primary_image' => 'nullable|exists:boarding_house_images,id',
            'primary_new_image' => 'nullable',
            'facilities' => 'nullable|array',
            'facilities.*' => 'exists:facilities,facility_id',
            'amenities' => 'nullable|string',
        ]);

        DB::beginTransaction();
        \Log::info('Bed space per room debug', [
            'input' => $request->input('bed_space_per_room'),
            'validated' => $validated['bed_space_per_room'] ?? 'not in validated',
            'space_type' => $validated['space_type'],
            'all_input' => $request->except(['_token', '_method', 'new_images'])
        ]);
        
        try {
            if ($request->has('removed_images')) {
                foreach ($request->removed_images as $imageId) {
                    $image = \App\Models\BoardingHouseImage::find($imageId);
                    if ($image) {
                        // Delete from storage
                        Storage::disk('public')->delete($image->image_path);
                        // Delete from database
                        $image->delete();
                    }
                }
            }

            // Handle new images
            if ($request->hasFile('bh_images')) {
                \Log::info('Processing new images', [
                    'file_count' => count($request->file('bh_images')),
                    'primary_new_image' => $request->input('primary_new_image')
                ]);
                
                $displayOrder = $boardingHouse->images()->max('display_order') ?? 0;
                $primaryNewImage = $request->input('primary_new_image');
                
                foreach ($request->file('bh_images') as $index => $image) {
                    $displayOrder++;
                    $originalName = $image->getClientOriginalName();
                    $imageName = time() . '_' . uniqid() . '_' . $originalName;
                    
                    \Log::info('Processing image ' . ($index + 1), [
                        'original_name' => $originalName,
                        'new_name' => $imageName,
                        'size' => $image->getSize(),
                        'mime' => $image->getMimeType()
                    ]);
                    
                    try {
                        $imagePath = $image->storeAs('boarding-houses', $imageName, 'public');
                        \Log::info('Image stored successfully', ['path' => $imagePath]);
                        
                        // Check if this image should be primary
                        $isPrimary = ($primaryNewImage === $originalName) || 
                                    ($primaryNewImage === null && $displayOrder === 1 && $boardingHouse->images()->count() === 0);
                        
                        \Log::info('Creating image record', [
                            'path' => $imagePath,
                            'is_primary' => $isPrimary,
                            'display_order' => $displayOrder
                        ]);
                        
                        $imageRecord = $boardingHouse->images()->create([
                            'image_path' => $imagePath,
                            'is_primary' => $isPrimary,
                            'display_order' => $displayOrder
                        ]);
                        
                        \Log::info('Image record created', ['id' => $imageRecord->id]);
                        
                        // If this is the only image, make it primary
                        if ($displayOrder === 1 && $boardingHouse->images()->count() === 1) {
                            \Log::info('Setting first image as primary', ['id' => $imageRecord->id]);
                            $boardingHouse->images()->where('id', '!=', $imageRecord->id)->update(['is_primary' => false]);
                            $imageRecord->update(['is_primary' => true]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error processing image: ' . $e->getMessage(), [
                            'file' => $originalName,
                            'exception' => $e
                        ]);
                        continue;
                    }
                }
            }

            // Handle primary image update
            if ($request->has('primary_image')) {
                // Reset all images to not primary
                $boardingHouse->images()->update(['is_primary' => false]);
                
                // Set the selected image as primary
                $primaryImage = $boardingHouse->images()->find($request->primary_image);
                if ($primaryImage) {
                    $primaryImage->update(['is_primary' => true]);
                    \Log::info('Primary image updated', [
                        'image_id' => $primaryImage->id,
                        'boarding_house_id' => $boardingHouse->id
                    ]);
                } else {
                    \Log::warning('Primary image not found', [
                        'image_id' => $request->primary_image,
                        'boarding_house_id' => $boardingHouse->id
                    ]);
                }
            } else {
                // If no primary image is set, set the first one as primary
                if ($boardingHouse->images()->where('is_primary', true)->doesntExist() && 
                    $boardingHouse->images()->exists()) {
                    $firstImage = $boardingHouse->images()->orderBy('display_order')->first();
                    if ($firstImage) {
                        $firstImage->update(['is_primary' => true]);
                        \Log::info('Set first image as primary', [
                            'image_id' => $firstImage->id,
                            'boarding_house_id' => $boardingHouse->id
                        ]);
                    }
                }
            }

            if (isset($validated['location'])) {
                $location = explode(',', $validated['location']);
                $validated['latitude'] = trim($location[0]);
                $validated['longitude'] = trim($location[1]);
                unset($validated['location']);
            }

            $oldContactPerson = $boardingHouse->contact_person;
            $isContactPersonChanged = $oldContactPerson !== $validated['contact_person'];

            // Update the boarding house with validated data (excluding image and other non-db fields)
            $updateData = [
                'name' => $validated['name'],
                'space_type' => $validated['space_type'],
                'description' => $validated['description'] ?? null,
                'bed_space_price' => $validated['bed_space_price'] ?? null,
                'bed_space_per_room' => in_array($validated['space_type'], ['Bed Space', 'Both']) ? ($validated['bed_space_per_room'] ?? 0) : 0,
                'room_space_price' => $validated['room_space_price'] ?? null,
                'price' => $validated['bed_space_price'] ?? $validated['room_space_price'] ?? 0, // Keep for backward compatibility
                'max_boarders' => in_array($validated['space_type'], ['Bed Space', 'Both']) ? $validated['max_boarders'] : null,
                'current_boarders' => in_array($validated['space_type'], ['Bed Space', 'Both']) ? ($validated['current_boarders'] ?? 0) : 0,
                'max_rooms' => in_array($validated['space_type'], ['Room Space', 'Both']) ? $validated['max_rooms'] : null,
                'available_rooms' => in_array($validated['space_type'], ['Room Space', 'Both']) ? ($validated['available_rooms'] ?? $validated['max_rooms'] ?? 1) : null,
                'address' => $validated['address'],
                'contact_person' => $validated['contact_person'],
                'contact_number' => $validated['contact_number'],
                'status' => $validated['status'],
                'amenities' => $validated['amenities'] ?? null,
            ];
            
            if (isset($validated['latitude']) && isset($validated['longitude'])) {
                $updateData['latitude'] = $validated['latitude'];
                $updateData['longitude'] = $validated['longitude'];
            }
            
            $boardingHouse->update($updateData);

            if ($isContactPersonChanged) {
                \App\Models\BoardingHouse::where('contact_person', $oldContactPerson)
                    ->where('id', '!=', $boardingHouse->id)
                    ->update(['contact_person' => $validated['contact_person']]);
                
                \Log::info('Updated contact person for all related boarding houses', [
                    'old_contact_person' => $oldContactPerson,
                    'new_contact_person' => $validated['contact_person'],
                    'updated_by' => $user->user_id,
                    'boarding_house_id' => $boardingHouse->id
                ]);
            }

            // Sync facilities
            if ($request->has('facilities')) {
                $boardingHouse->facilities()->sync($request->facilities);
            } else {
                $boardingHouse->facilities()->detach();
            }
            
            // Eager load the updated relationships
            $boardingHouse->load('images', 'facilities');

            DB::commit();

            return redirect()->route('admin.enlistments.edit', $boardingHouse)
                ->with('success', 'Boarding house updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating boarding house: ' . $e->getMessage());
            return back()->with('error', 'Error updating boarding house. Please try again.')
                        ->withInput();
        }
        
    }
}