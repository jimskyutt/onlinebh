@extends('layouts.admin')

@section('title', 'Edit Boarding House | Online BH Finder')

@push('styles')
    @vite(['resources/css/add_bh.css'])
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Edit Boarding House</h1>
        <a href="{{ route('admin.enlistments.show', $boardingHouse->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4" style="width: 800px; margin: 0 auto;">
        <div class="card-body">
            <form action="{{ route('admin.enlistments.update', $boardingHouse->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="primary_image" id="primary_image" value="{{ $boardingHouse->images->where('is_primary', true)->first()?->id }}">
                <input type="hidden" name="primary_new_image" id="primary_new_image">
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">Boarding House Images <small class="text-muted">(Upload up to 5 images, first image will be used as main)</small></label>
                        <div class="row" id="imagePreviewContainer">
                            @if($boardingHouse->images->count() > 0)
                                {{-- Sort images to show primary first --}}
                                @php
                                    $sortedImages = $boardingHouse->images->sortByDesc('is_primary');
                                @endphp
                                @foreach($sortedImages as $key => $image)
                                    <div class="col-md-6 col-lg-4 mb-3 image-preview-item" data-id="{{ $image->id }}">
                                        <div class="card h-100">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                 class="card-img-top" 
                                                 style="height: 180px; object-fit: cover;" 
                                                 alt="Image {{ $key }}">
                                            <div class="card-body p-2 text-center">
                                                <span class="badge {{ $image->is_primary ? 'bg-primary' : 'bg-secondary' }}" id="badge-{{ $image->id }}">
                                                    {{ $image->is_primary ? 'Primary Image' : 'Image ' . ($key + 1) }}
                                                </span>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary set-primary {{ $image->is_primary ? 'd-none' : '' }}" 
                                                            data-id="{{ $image->id }}"
                                                            id="set-primary-{{ $image->id }}">
                                                        <i class="fas fa-star"></i> Set Primary
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-image" 
                                                            data-id="{{ $image->id }}"
                                                            onclick="handleImageRemoval(this)">
                                                        <i class="fas fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center">
                                    <div class="border rounded p-3" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <div class="text-center">
                                            <i class="fas fa-images fa-3x text-muted mb-2"></i>
                                            <p class="mb-0">No images uploaded</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="text-center mt-3">
                            <label class="btn btn-outline-primary" for="bh_images" id="selectImagesBtn">
                                <i class="fas fa-plus me-2"></i>Add More Images
                            </label>
                            <input type="file" class="d-none" id="bh_images" name="bh_images[]" accept="image/*" multiple>
                            <div class="form-text">You can upload up to 5 images in total. Accepted formats: JPG, PNG, GIF. Max 2MB per image.</div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Boarding House Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required value="{{ $boardingHouse->name }}">
                        </div>
                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" required 
                                value="{{ $boardingHouse->contact_person }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="space_type" class="form-label">Space Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="space_type" name="space_type" required>
                                <option value="" disabled {{ !in_array($boardingHouse->space_type, ['Bed Space', 'Room Space', 'Both']) ? 'selected' : '' }}>Select space type</option>
                                <option value="Bed Space" {{ $boardingHouse->space_type == 'Bed Space' ? 'selected' : '' }}>Bed Space</option>
                                <option value="Room Space" {{ $boardingHouse->space_type == 'Room Space' ? 'selected' : '' }}>Room Space</option>
                                <option value="Both" {{ $boardingHouse->space_type == 'Both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>

                        <div id="bedSpaceContainer" style="display: {{ in_array($boardingHouse->space_type, ['Bed Space', 'Both']) ? 'block' : 'none' }};">
                            <div class="mb-3 price-field" id="bedSpacePriceField">
                                <label for="bed_space_price" class="form-label">Bed Space Monthly Rate (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="bed_space_price" name="bed_space_price" min="0" step="0.01"
                                    value="{{ isset($boardingHouse->bed_space_price) ? (floor($boardingHouse->bed_space_price) == $boardingHouse->bed_space_price ? number_format($boardingHouse->bed_space_price, 0, '.', '') : $boardingHouse->bed_space_price) : '' }}"
                                    {{ in_array($boardingHouse->space_type, ['Bed Space', 'Both']) ? 'required' : '' }}>
                            </div>
                            <div id="bedSpacePerRoomInContainer" style="display: {{ $boardingHouse->space_type == 'Both' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="bed_space_per_room_both" class="form-label">Bed Space per Room <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control bed-space-per-room" id="bed_space_per_room_both" name="bed_space_per_room" min="1" value="{{ $boardingHouse->bed_space_per_room ?? '1' }}">
                                </div>
                            </div>
                        </div>

                        <div id="roomSpacePriceLeftContainer" style="display: {{ $boardingHouse->space_type == 'Room Space' ? 'block' : 'none' }};">
                            <div class="mb-3 price-field">
                                <label for="room_space_price_left" class="form-label">Room Space Monthly Rate (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="room_space_price_left" name="room_space_price" min="0" step="0.01"
                                    value="{{ isset($boardingHouse->room_space_price) ? (floor($boardingHouse->room_space_price) == $boardingHouse->room_space_price ? number_format($boardingHouse->room_space_price, 0, '.', '') : $boardingHouse->room_space_price) : '' }}"
                                    {{ $boardingHouse->space_type == 'Room Space' ? 'required' : '' }}>
                            </div>
                        </div>
                    </div>
                        
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address" class="form-label">Complete Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="address" name="address" required value="{{ $boardingHouse->address }}">
                                <button class="btn btn-bh" type="button" id="addressSearch">
                                    <i class="fas fa-location"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                pattern="[0-9+\- ]+" title="Please enter a valid contact number" required
                                value="{{ $boardingHouse->contact_number }}">
                        </div>
                            
                        <div class="mb-3 d-none">
                            <label for="location" class="form-label">Location (Latitude, Longitude) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="location" name="location" 
                                    value="{{ $boardingHouse->latitude }}, {{ $boardingHouse->longitude }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control">
                                <option value="Available" {{ $boardingHouse->status == 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="Full" {{ $boardingHouse->status == 'Full' ? 'selected' : '' }}>Full</option>
                                <option value="Under Maintenance" {{ $boardingHouse->status == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            </select>
                        </div>
                        
                        <div id="bedSpacePerRoomContainer" style="display: {{ $boardingHouse->space_type == 'Bed Space' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="bed_space_per_room_single" class="form-label">Bed Space per Room <span class="text-danger">*</span></label>
                                <input type="number" class="form-control bed-space-per-room" id="bed_space_per_room_single" name="bed_space_per_room" min="1" value="{{ $boardingHouse->bed_space_per_room ?? '1' }}">
                            </div>
                        </div>

                        <div id="roomSpacePriceRightContainer" style="display: {{ $boardingHouse->space_type == 'Both' ? 'block' : 'none' }};">
                            <div class="mb-3 price-field">
                                <label for="room_space_price" class="form-label">Room Space Monthly Rate (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="room_space_price" name="room_space_price" min="0" step="0.01"
                                    value="{{ isset($boardingHouse->room_space_price) ? (floor($boardingHouse->room_space_price) == $boardingHouse->room_space_price ? number_format($boardingHouse->room_space_price, 0, '.', '') : $boardingHouse->room_space_price) : '' }}"
                                    {{ $boardingHouse->space_type == 'Both' ? 'required' : '' }}>
                            </div>
                        </div>
                    </div>
                    
                    <div id="boardersSection" class="col-md-12 mb-3" style="display: {{ in_array($boardingHouse->space_type, ['Bed Space', 'Both']) ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="current_boarders" class="form-label">Current Boarders <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="current_boarders" name="current_boarders" min="0" 
                                       value="{{ $boardingHouse->current_boarders }}" 
                                       {{ in_array($boardingHouse->space_type, ['Bed Space', 'Both']) ? 'required' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="max_boarders" class="form-label">Maximum Boarders <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_boarders" name="max_boarders" min="1" 
                                       value="{{ $boardingHouse->max_boarders }}"
                                       {{ in_array($boardingHouse->space_type, ['Bed Space', 'Both']) ? 'required' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div id="roomsSection" class="col-md-12 mb-3" style="display: {{ in_array($boardingHouse->space_type, ['Room Space', 'Both']) ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="available_rooms" class="form-label">Available Rooms <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="available_rooms" name="available_rooms" min="0"
                                       value="{{ $boardingHouse->available_rooms ?? 0 }}"
                                       {{ in_array($boardingHouse->space_type, ['Room Space', 'Both']) ? 'required' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="max_rooms" class="form-label">Maximum Rooms <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_rooms" name="max_rooms" min="1"
                                       value="{{ $boardingHouse->max_rooms ?? 1 }}"
                                       {{ in_array($boardingHouse->space_type, ['Room Space', 'Both']) ? 'required' : '' }}>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label">Facilities <span class="text-danger">*</span></label>
                    <div class="row">
                        @php
                            $boardingHouseFacilities = $boardingHouse->facilities->pluck('facility_id')->toArray();
                        @endphp
                        @foreach($facilities as $facility)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="facilities[]" 
                                           value="{{ $facility->facility_id }}" 
                                           id="facility_{{ $facility->facility_id }}"
                                           {{ in_array($facility->facility_id, $boardingHouseFacilities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="facility_{{ $facility->facility_id }}">
                                        {{ $facility->facility_name }}
                                    </label>    
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label for="amenities" class="form-label">Additional Amenities (comma-separated)</label>
                    <textarea class="form-control" id="amenities" name="amenities"
                            placeholder="e.g., Study Area, Common Kitchen, Roof Deck">{{ $boardingHouse->amenities ?? '' }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ $boardingHouse->description ?? '' }}</textarea>
                </div>
                    
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-bh" id="saveBtn">
                        <i class="fas fa-save"></i> Save Boarding House
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Select Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="map"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-bh" id="confirmLocation">Confirm Location</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@include('partials.credentials-modal')
@push('scripts')
    @vite(['resources/js/add_bh.js', 'resources/js/map.js'])
    <script>
        const removedImages = new Set();
        let newFiles = [];
        
        function handleImageRemoval(button) {
            const imageId = button.getAttribute('data-id');
            const imageCard = button.closest('.image-preview-item');
            
            removedImages.add(imageId);
            imageCard.style.display = 'none';
            
            updateRemovedImagesInput();
        }
        
    function updateRemovedImagesInput() {
            let removedInput = document.getElementById('removed_images');
            if (!removedInput) {
                removedInput = document.createElement('input');
                removedInput.type = 'hidden';
                removedInput.name = 'removed_images';
                removedInput.id = 'removed_images';
                document.querySelector('form').appendChild(removedInput);
            }
            removedInput.value = Array.from(removedImages).join(',');
        }
        
        function updatePrimaryImageStates(imageId, options = {}) {
            const {
                updateBadge = true,
                updateButton = true,
                updateInput = true,
                moveToTop = true
            } = options;
            
            const container = document.getElementById('imagePreviewContainer');
            const isNewImage = imageId.startsWith('new-');
            const selector = isNewImage 
                ? `.new-image-preview[data-temp-id="${imageId}"]` 
                : `.image-preview-item[data-id="${imageId}"]`;
            const imageItem = document.querySelector(selector);
            
            if (!imageItem) return;
            
            if (updateBadge) {
                document.querySelectorAll('.badge').forEach(badge => {
                    badge.classList.remove('bg-primary');
                    badge.classList.add('bg-secondary');
                    
                    if (badge.textContent.includes('Primary')) {
                        const imageNumber = badge.textContent.match(/Image (\d+)/);
                        if (imageNumber && imageNumber[1]) {
                            badge.textContent = `Image ${imageNumber[1]}`;
                        } else {
                            badge.textContent = 'Image 1';
                        }
                    }
                });
                
                const badge = document.getElementById(`badge-${imageId}`);
                if (badge) {
                    badge.classList.remove('bg-secondary');
                    badge.classList.add('bg-primary');
                    badge.textContent = 'Primary Image';
                }
            }
            
            if (updateButton) {
                document.querySelectorAll('.set-primary').forEach(btn => {
                    btn.classList.remove('d-none');
                });
                
                const primaryBtn = imageItem.querySelector('.set-primary');
                if (primaryBtn) {
                    primaryBtn.classList.add('d-none');
                }
            }
            
            if (moveToTop && container && imageItem) {
                container.insertBefore(imageItem, container.firstChild);
                
                document.querySelectorAll('.image-preview-item, .new-image-preview').forEach((item, index) => {
                    const img = item.querySelector('img');
                    if (img) {
                        const altText = img.alt.replace(/\d+$/, '') + (index + 1);
                        img.alt = altText;
                    }
                    const badge = item.querySelector('.badge:not(.bg-primary)');
                    if (badge && !badge.textContent.includes('Primary')) {
                        badge.textContent = `Image ${index + 1}`;
                    }
                });
            }
        }
        
        function handleNewImages(input) {
            const container = document.getElementById('imagePreviewContainer');
            const files = Array.from(input.files);
            const existingPrimary = document.querySelector('.badge.bg-primary');
            
            const validFiles = files.filter(file => {
                if (!file.type.match('image.*')) {
                    alert(`File ${file.name} is not a valid image.`);
                    return false;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert(`File ${file.name} is too large. Maximum size is 2MB.`);
                    return false;
                }
                return true;
            });
            
            newFiles = [...newFiles, ...validFiles];
            
            const dataTransfer = new DataTransfer();
            newFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
            
            updateImagePreviews();
            
            if (!existingPrimary && (newFiles.length > 0 || container.querySelector('.image-preview-item'))) {
                const firstImage = container.querySelector('.image-preview-item:not([style*="display: none"]), .new-image-preview');
                if (firstImage) {
                    const imageId = firstImage.getAttribute('data-id') || firstImage.getAttribute('data-temp-id');
                    setPrimaryImage(imageId);
                }
            }
        }
        
        function setPrimaryImage(imageId) {
            const isNewImage = imageId.startsWith('new-');
            
            if (isNewImage) {
                const index = parseInt(imageId.split('-')[1]);
                document.getElementById('primary_new_image').value = newFiles[index].name;
                document.getElementById('primary_image').value = '';
            } else {
                document.getElementById('primary_image').value = imageId;
                document.getElementById('primary_new_image').value = '';
            }
            
            updatePrimaryImageStates(imageId, {
                updateBadge: true,
                updateButton: true,
                updateInput: false,
                moveToTop: true
            });
        }
        
        function updateImagePreviews() {
            const container = document.getElementById('imagePreviewContainer');
            const existingPreviews = container.querySelectorAll('.image-preview-item');
            const existingImages = Array.from(existingPreviews).filter(el => el.style.display !== 'none');
            
            const newImagePreviews = container.querySelectorAll('.new-image-preview');
            newImagePreviews.forEach(el => el.remove());
            
           newFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-3 new-image-preview';
                    const tempId = `new-${index}`;
                    col.setAttribute('data-temp-id', tempId);
                    
                    const primaryNewImage = document.getElementById('primary_new_image').value;
                    const isPrimary = primaryNewImage === file.name;
                    
                    col.innerHTML = `
                        <div class="card h-100">
                            <img src="${e.target.result}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="New Image ${index + 1}">
                            <div class="card-body p-2 text-center">
                                <span class="badge ${isPrimary ? 'bg-primary' : 'bg-secondary'}" id="badge-${tempId}">
                                    ${isPrimary ? 'Primary Image' : 'New Image'}
                                </span>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary set-primary ${isPrimary ? 'd-none' : ''}" 
                                            data-id="${tempId}">
                                        <i class="fas fa-star"></i> Set Primary
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-new-image" 
                                            data-index="${index}">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    container.appendChild(col);
                    
                    if (index === 0 && !document.querySelector('.badge.bg-primary')) {
                        const primaryInput = document.getElementById('primary_new_image');
                        if (primaryInput) {
                            primaryInput.value = file.name;
                        }
                    }
                };
                reader.readAsDataURL(file);
            });
            
            const totalImages = existingImages.length + newFiles.length;
            const addMoreBtn = document.getElementById('selectImagesBtn');
            if (totalImages >= 5) {
                addMoreBtn.style.display = 'none';
            } else {
                addMoreBtn.style.display = 'inline-block';
            }
        }
        
        function handleNewImageActions(event) {
            if (event.target.closest('.remove-new-image')) {
                const button = event.target.closest('.remove-new-image');
                const index = parseInt(button.getAttribute('data-index'));
                
                const isPrimary = newFiles[index].name === document.getElementById('primary_new_image').value;
                
                newFiles.splice(index, 1);
                
                const fileInput = document.getElementById('bh_images');
                const dataTransfer = new DataTransfer();
                newFiles.forEach(file => dataTransfer.items.add(file));
                fileInput.files = dataTransfer.files;
                
                if (isPrimary) {
                    document.getElementById('primary_new_image').value = '';
                }
                
                updateImagePreviews();
                
                if (isPrimary) {
                    const container = document.getElementById('imagePreviewContainer');
                    const firstImage = container.querySelector('.image-preview-item:not([style*="display: none"]), .new-image-preview');
                    if (firstImage) {
                        const imageId = firstImage.getAttribute('data-id') || firstImage.getAttribute('data-temp-id');
                        setPrimaryImage(imageId);
                    }
                }
            }
            
            if (event.target.closest('.set-primary')) {
                const button = event.target.closest('.set-primary');
                const imageId = button.getAttribute('data-id');
                setPrimaryImage(imageId);
                console.log('Primary image set to:', imageId);
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('bh_images');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    handleNewImages(this);
                });
            }
            
            const primaryImageId = document.getElementById('primary_image')?.value;
            const existingPrimary = document.querySelector('.badge.bg-primary');
            
            if (primaryImageId) {
                updatePrimaryImageStates(primaryImageId, {
                    updateBadge: false,
                    updateButton: true,
                    updateInput: false,
                    moveToTop: false
                });
            } else if (existingPrimary) {
                const imageId = existingPrimary.closest('.image-preview-item').getAttribute('data-id');
                updatePrimaryImageStates(imageId, {
                    updateBadge: true,
                    updateButton: true,
                    updateInput: true,
                    moveToTop: true
                });
            }
            
            document.addEventListener('click', handleNewImageActions);
        
            function logFormData(formData) {
                console.log('Form Data:');
                for (let pair of formData.entries()) {
                    if (pair[0] === 'new_images') {
                        console.log(pair[0] + ': ' + pair[1].name + ' (' + pair[1].size + ' bytes)');
                    } else {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                }
            }
            
            document.querySelector('form').addEventListener('submit', function(e) {
                console.log('Form submission started');
                console.log('New files to upload:', newFiles);
                
                const primaryImageInput = document.getElementById('primary_image');
                const hasExistingImages = document.querySelector('.image-preview-item:not([style*="display: none"])');
                const hasNewImages = newFiles.length > 0;
                
                console.log('Primary image input value:', primaryImageInput ? primaryImageInput.value : 'none');
                console.log('Primary new image value:', document.getElementById('primary_new_image').value);
                console.log('Has existing images:', !!hasExistingImages);
                console.log('Has new images:', hasNewImages);
                
                if ((!primaryImageInput || !primaryImageInput.value) && hasExistingImages) {
                    const firstImage = document.querySelector('.image-preview-item:not([style*="display: none"])');
                    if (firstImage) {
                        const imageId = firstImage.getAttribute('data-id');
                        updatePrimaryImageStates(imageId, {
                            updateBadge: true,
                            updateButton: true,
                            updateInput: true,
                            moveToTop: true
                        });
                    }
                }
                else if ((!primaryImageInput || !primaryImageInput.value) && hasNewImages) {
                    document.getElementById('primary_new_image').value = newFiles[0].name;
                }
                
                if (hasNewImages) {
                    e.preventDefault();
                    const form = e.target;
                    const formData = new FormData(form);
                    
                    newFiles.forEach((file, index) => {
                        formData.append('new_images[]', file);
                    });
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-HTTP-Method-Override': 'PUT'
                        }
                    })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            return response.text().then(text => {
                                console.error('Error:', text);
                                alert('Error updating boarding house. Please try again.');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating boarding house. Please try again.');
                    });
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const spaceTypeSelect = document.getElementById('space_type');
            const bedSpaceContainer = document.getElementById('bedSpaceContainer');
            const bedSpacePerRoomContainer = document.getElementById('bedSpacePerRoomContainer');
            const bedSpacePerRoomInContainer = document.getElementById('bedSpacePerRoomInContainer');
            const roomSpacePriceLeftContainer = document.getElementById('roomSpacePriceLeftContainer');
            const roomSpacePriceRightContainer = document.getElementById('roomSpacePriceRightContainer');
            const bedSpacePriceInput = document.getElementById('bed_space_price');
            const roomSpacePriceInput = document.getElementById('room_space_price');
            const roomSpacePriceLeftInput = document.getElementById('room_space_price_left');
            const boardersSection = document.getElementById('boardersSection');
            const roomsSection = document.getElementById('roomsSection');
            const currentBoardersInput = document.getElementById('current_boarders');
            const maxBoardersInput = document.getElementById('max_boarders');
            const availableRoomsInput = document.getElementById('available_rooms');
            const maxRoomsInput = document.getElementById('max_rooms');
            const bedSpacePerRoomInputs = document.querySelectorAll('.bed-space-per-room');

            function updateFormFields() {
                if (bedSpaceContainer) bedSpaceContainer.style.display = 'none';
                if (bedSpacePerRoomContainer) bedSpacePerRoomContainer.style.display = 'none';
                if (bedSpacePerRoomInContainer) bedSpacePerRoomInContainer.style.display = 'none';
                if (roomSpacePriceLeftContainer) roomSpacePriceLeftContainer.style.display = 'none';
                if (roomSpacePriceRightContainer) roomSpacePriceRightContainer.style.display = 'none';
                if (boardersSection) boardersSection.style.display = 'none';
                if (roomsSection) roomsSection.style.display = 'none';
                
                [bedSpacePriceInput, roomSpacePriceInput, roomSpacePriceLeftInput, 
                 currentBoardersInput, maxBoardersInput, availableRoomsInput, 
                 maxRoomsInput].concat(Array.from(bedSpacePerRoomInputs)).forEach(input => {
                    if (input) input.required = false;
                });

                switch(spaceTypeSelect.value) {
                    case 'Bed Space':
                        if (bedSpaceContainer) {
                            bedSpaceContainer.style.display = 'block';
                            if (bedSpacePriceInput) bedSpacePriceInput.required = true;
                            if (bedSpacePerRoomInContainer) bedSpacePerRoomInContainer.style.display = 'none';
                        }
                        if (bedSpacePerRoomContainer) {
                            bedSpacePerRoomContainer.style.display = 'block';
                            document.getElementById('bed_space_per_room_single').required = true;
                        }
                        if (boardersSection) {
                            boardersSection.style.display = 'block';
                            if (currentBoardersInput) currentBoardersInput.required = true;
                            if (maxBoardersInput) maxBoardersInput.required = true;
                        }
                        break;
                        
                    case 'Room Space':
                        if (roomSpacePriceLeftContainer) roomSpacePriceLeftContainer.style.display = 'block';
                        if (roomSpacePriceLeftInput) roomSpacePriceLeftInput.required = true;
                        if (roomsSection) roomsSection.style.display = 'block';
                        if (availableRoomsInput) availableRoomsInput.required = true;
                        if (maxRoomsInput) maxRoomsInput.required = true;
                        if (bedSpacePerRoomInContainer) bedSpacePerRoomInContainer.style.display = 'none';
                        if (bedSpacePerRoomContainer) bedSpacePerRoomContainer.style.display = 'none';
                        if (roomSpacePriceLeftInput && roomSpacePriceInput) {
                            roomSpacePriceInput.value = roomSpacePriceLeftInput.value;
                        }
                        break;
                        
                    case 'Both':
                        if (bedSpaceContainer) {
                            bedSpaceContainer.style.display = 'block';
                            if (bedSpacePriceInput) bedSpacePriceInput.required = true;
                            if (bedSpacePerRoomInContainer) {
                                bedSpacePerRoomInContainer.style.display = 'block';
                                document.getElementById('bed_space_per_room_both').required = true;
                            }
                        }
                        if (bedSpacePerRoomContainer) {
                            bedSpacePerRoomContainer.style.display = 'none';
                        }
                        if (roomSpacePriceRightContainer) {
                            roomSpacePriceRightContainer.style.display = 'block';
                            if (roomSpacePriceInput) roomSpacePriceInput.required = true;
                        }
                        if (boardersSection) boardersSection.style.display = 'block';
                        if (roomsSection) roomsSection.style.display = 'block';
                        [currentBoardersInput, maxBoardersInput, availableRoomsInput, maxRoomsInput].forEach(input => {
                            if (input) input.required = true;
                        });
                        break;
                }
                
                if (roomSpacePriceInput && roomSpacePriceLeftInput) {
                    roomSpacePriceInput.addEventListener('input', function() {
                        roomSpacePriceLeftInput.value = this.value;
                    });
                    roomSpacePriceLeftInput.addEventListener('input', function() {
                        roomSpacePriceInput.value = this.value;
                    });
                }
            }

            if (spaceTypeSelect) {
                updateFormFields();
                spaceTypeSelect.addEventListener('change', updateFormFields);
            }

            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.transition = 'opacity 0.5s ease-out';
                    successAlert.style.opacity = '0';
                    setTimeout(() => successAlert.remove(), 500);
                }, 2000);
            }
        });
    </script>
@endpush
@endsection
