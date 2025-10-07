@extends('layouts.admin')

@section('title', 'Enlist Boarding House | Online BH Finder')

@push('styles')
    @vite(['resources/css/add_bh.css'])
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Enlist New Boarding House</h1>
        <a href="{{ $backUrl }}" class="btn btn-secondary">
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
            <form action="{{ route('admin.enlistments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf             
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">Boarding House Images <span class="text-danger">*</span> <small class="text-muted">(Upload 1-5 images, first image will be used as main)</small></label>
                        <div class="row" id="imagePreviewContainer">
                            <div class="col-12 text-center">
                                <div class="border rounded p-3" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <div class="text-center">
                                        <i class="fas fa-images fa-3x text-muted mb-2"></i>
                                        <p class="mb-0">No images selected</p>
                                        <small class="text-muted">Upload 1-5 images</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <label class="btn btn-outline-primary" for="bh_images" style="margin: 0 auto;">
                                <i class="fas fa-upload me-2"></i>Select Images
                            </label>
                            <input type="file" class="d-none" id="bh_images" name="bh_images[]" accept="image/*" multiple onchange="previewImages(this)">
                            <div class="form-text">Accepted formats: JPG, PNG, GIF. Max 2MB per image.</div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Boarding House Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" required 
                                value="">
                        </div>

                        <div class="mb-3">
                            <label for="space_type" class="form-label">Space Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="space_type" name="space_type" required>
                                <option value="" disabled selected>Select space type</option>
                                <option value="Bed Space">Bed Space</option>
                                <option value="Room Space">Room Space</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>

                        <div id="bedSpaceContainer" style="display: none;">
                            <div class="mb-3 price-field" id="bedSpacePriceField">
                                <label for="bed_space_price" class="form-label">Bed Space Monthly Rate (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="bed_space_price" name="bed_space_price" min="0" step="0.01">
                            </div>
                            <div id="bedSpacePerRoomInContainer" style="display: none;">
                                <div class="mb-3">
                                    <label for="bed_space_per_room_both" class="form-label">Bed Space per Room <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control bed-space-per-room" id="bed_space_per_room_both" name="bed_space_per_room" min="1" value="">
                                </div>
                            </div>
                        </div>

                        <div id="roomSpacePriceLeftContainer" style="display: none;">
                            <div class="mb-3 price-field">
                                <label for="room_space_price" class="form-label">Room Space Monthly Rate (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="room_space_price_left" name="room_space_price" min="0" step="0.01">
                            </div>
                        </div>
                        
                    </div>
                        
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address" class="form-label">Complete Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="address" name="address" required>
                                <button class="btn btn-bh" type="button" id="addressSearch">
                                    <i class="fas fa-location"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                pattern="[0-9+\- ]+" title="Please enter a valid contact number" required
                                value="">
                        </div>
                            
                        <div class="mb-3 d-none">
                            <label for="location" class="form-label">Location (Latitude,Longitude) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control">
                                <option value="Available">Available</option>
                                <option value="Full">Full</option>
                                <option value="Under Maintenance">Under Maintenance</option>
                            </select>
                        </div>
                        
                        <div id="bedSpacePerRoomContainer" style="display: none;">
                            <div class="mb-3">
                                <label for="bed_space_per_room_single" class="form-label">Bed Space per Room <span class="text-danger">*</span></label>
                                <input type="number" class="form-control bed-space-per-room" id="bed_space_per_room_single" name="bed_space_per_room" min="1" value="">
                            </div>
                        </div>
                        
                        <div id="roomSpacePriceRightContainer" style="display: none;">
                            <div class="mb-3 price-field">
                                <label for="room_space_price" class="form-label">Room Space Monthly Rate (₱) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="room_space_price" name="room_space_price" min="0" step="0.01">
                            </div>
                        </div>
                        
                    </div>
                    <div id="boardersSection" class="col-md-12 mb-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="current_boarders" class="form-label">Current Boarders <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="current_boarders" name="current_boarders" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="max_boarders" class="form-label">Maximum Boarders <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_boarders" name="max_boarders" min="1">
                            </div>
                        </div>
                    </div>

                    <div id="roomsSection" class="col-md-12 mb-3" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="available_rooms" class="form-label">Available Rooms <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="available_rooms" name="available_rooms" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="max_rooms" class="form-label">Maximum Rooms <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_rooms" name="max_rooms" min="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Facilities <span class="text-danger">*</span></label>
                    <div class="row">
                        @foreach($facilities as $facility)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilities[]" value="{{ $facility->facility_id }}" id="facility_{{ $facility->facility_id }}">
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
                            placeholder="e.g., Study Area, Common Kitchen, Roof Deck"></textarea>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
                            document.getElementById('bed_space_per_room_single').required = false;
                        }
                        if (roomSpacePriceRightContainer) {
                            roomSpacePriceRightContainer.style.display = 'block';
                            if (roomSpacePriceInput) roomSpacePriceInput.required = true;
                        }
                        if (boardersSection) {
                            boardersSection.style.display = 'block';
                            if (currentBoardersInput) currentBoardersInput.required = true;
                            if (maxBoardersInput) maxBoardersInput.required = true;
                        }
                        if (roomsSection) {
                            roomsSection.style.display = 'block';
                            if (availableRoomsInput) availableRoomsInput.required = true;
                            if (maxRoomsInput) maxRoomsInput.required = true;
                        }
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
        });

        let currentFiles = [];
        
        window.removeImage = function(index) {
            currentFiles.splice(index, 1);
            
            const dataTransfer = new DataTransfer();
            currentFiles.forEach(file => dataTransfer.items.add(file));
            const fileInput = document.getElementById('bh_images');
            fileInput.files = dataTransfer.files;
            
            previewImages(fileInput);
        };
        
        window.previewImages = function(input) {
            const container = document.getElementById('imagePreviewContainer');
            if (!container) return;
            
            container.innerHTML = ''; 
            
            if (input && input.files && input.files.length > 0) {
                currentFiles = Array.from(input.files);
            }
            
            if (currentFiles.length > 5) {
                alert('You can only upload up to 5 images');
                input.value = '';
                currentFiles = [];
                showNoImagesMessage(container);
                return;
            }

            if (currentFiles.length > 0) {
                const row = document.createElement('div');
                row.className = 'row';
                container.appendChild(row);
                
                currentFiles.forEach((file, index) => {
                    if (!file.type.match('image.*')) {
                        alert('Please select valid image files only');
                        input.value = '';
                        currentFiles = [];
                        showNoImagesMessage(container);
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        alert('Image size must be less than 2MB');
                        input.value = '';
                        currentFiles = [];
                        showNoImagesMessage(container);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4 mb-3';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-fluid rounded" alt="Preview" style="height: 200px; width: 100%; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute" 
                                        style="top: 5px; right: 5px; padding: 0.15rem 0.35rem; border-radius: 50%;"
                                        onclick="removeImage(${index})">
                                    <i class="fas fa-times"></i>
                                </button>
                                <span class="position-absolute bottom-0 start-0 badge bg-${index === 0 ? 'primary' : 'secondary'} mb-2 ms-2">
                                    ${index === 0 ? 'Primary' : 'Image ' + (index + 1)}
                                </span>
                                <input type="hidden" name="display_order[]" value="${index + 1}">
                            </div>
                        `;
                        row.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                showNoImagesMessage(container);
            }
        };
        
        function showNoImagesMessage(container) {
            if (!container) return;
            container.innerHTML = `
                <div class="col-12 text-center">
                    <div class="border rounded p-3" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                        <div class="text-center">
                            <i class="fas fa-images fa-3x text-muted mb-2"></i>
                            <p class="mb-0">No images selected</p>
                            <small class="text-muted">Upload 1-5 images</small>
                        </div>
                    </div>
                </div>`;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
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