@extends('layouts.admin')
@section('title', $boardingHouse->name . ' | Boarding House Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Boarding House Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ $backUrl }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('admin.enlistments.edit', $boardingHouse->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">
                <i class="fas fa-trash me-1"></i> Delete
            </button>
            <form action="{{ route('admin.enlistments.destroy', $boardingHouse->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body p-0">
                    @if($boardingHouse->images->count() > 0)
                        <div id="bhCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                @foreach($boardingHouse->images as $key => $image)
                                    <button type="button" data-bs-target="#bhCarousel" 
                                            data-bs-slide-to="{{ $key }}" 
                                            class="{{ $key === 0 ? 'active' : '' }}" 
                                            aria-current="{{ $key === 0 ? 'true' : 'false' }}" 
                                            aria-label="Slide {{ $key + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($boardingHouse->images as $key => $image)
                                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             class="d-block w-100" 
                                             alt="{{ $boardingHouse->name }} - Image {{ $key + 1 }}"
                                             style="height: 400px; object-fit: cover;">
                                        @if($image->is_primary)
                                            <div class="carousel-caption d-none d-md-block">
                                                <span class="badge bg-primary">Main Image</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#bhCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#bhCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    @else
                        <div class="bg-light text-center py-5" style="height: 400px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-home fa-5x text-muted"></i>
                            <p class="w-100">No images available</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Details</h6>
                    <span class="badge bg-{{ $boardingHouse->status === 'Available' ? 'success' : 'warning' }} p-2">
                        {{ $boardingHouse->status }}
                    </span>
                </div>
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ $boardingHouse->name }}</h4>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i> {{ $boardingHouse->address }}</p>
                            <p><i class="fas fa-tag text-primary me-2"></i> 
                                <strong>{{ $boardingHouse->space_type }}</strong>
                            </p>
                            @if(in_array($boardingHouse->space_type, ['Bed Space', 'Both']))
                                <p><i class="fas fa-bed text-primary me-2"></i> 
                                    <strong>₱{{ number_format($boardingHouse->bed_space_price, $boardingHouse->bed_space_price == floor($boardingHouse->bed_space_price) ? 0 : 2) }}</strong> / month (Bed Space)
                                </p>
                            @endif
                            @if(in_array($boardingHouse->space_type, ['Room Space', 'Both']))
                                <p><i class="fas fa-home text-primary me-2"></i> 
                                    <strong>₱{{ number_format($boardingHouse->room_space_price, $boardingHouse->room_space_price == floor($boardingHouse->room_space_price) ? 0 : 2) }}</strong> / month (Room Space)
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if(in_array($boardingHouse->space_type, ['Bed Space', 'Both']))
                                <p><i class="fas fa-users text-primary me-2"></i> 
                                    {{ $boardingHouse->current_boarders }} / {{ $boardingHouse->max_boarders }} boarders
                                </p>
                            @endif
                            @if(in_array($boardingHouse->space_type, ['Room Space', 'Both']))
                                <p><i class="fas fa-door-open text-primary me-2"></i>
                                    {{ $boardingHouse->available_rooms }} / {{ $boardingHouse->max_rooms }} rooms available
                                </p>
                            @endif
                            <p><i class="fas fa-phone text-primary me-2"></i> {{ $boardingHouse->contact_number }}</p>
                            @if(in_array($boardingHouse->space_type, ['Bed Space', 'Both']))
                                <p><i class="fas fa-bed text-primary me-2"></i> 
                                    {{ $boardingHouse->bed_space_per_room }} beds per room
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($boardingHouse->amenities)
                        <h5 class="mt-4 mb-3">Amenities</h5>
                        <div class="row">
                            @foreach(explode(',', $boardingHouse->amenities) as $amenity)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <span>{{ trim($amenity) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($boardingHouse->facilities && $boardingHouse->facilities->count() > 0)
                        <h5 class="mt-4 mb-3">Facilities</h5>
                        <div class="row">
                            @foreach($boardingHouse->facilities as $facility)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <span>{{ $facility->facility_name ?? '' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($boardingHouse->description)
                        <div class="mt-4">
                            <h5>Description</h5>
                            <p class="text-muted">{{ $boardingHouse->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-primary me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold">Contact Person</p>
                            <p class="mb-0">{{ $boardingHouse->contact_person }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-primary me-3">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold">Contact Number</p>
                            <p class="mb-0">{{ $boardingHouse->contact_number }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-primary me-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold">Address</p>
                            <p class="mb-0">{{ $boardingHouse->address }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $boardingHouse->latitude }},{{ $boardingHouse->longitude }}" 
                           target="_blank" 
                           class="btn btn-outline-primary w-100">
                            <i class="fas fa-directions me-2"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Location</h6>
                </div>
                <div class="card-body p-0" style="height: 300px;">
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.del-conf')
@endsection

@push('styles')
    <style>
        .carousel-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 0 5px;
            background-color: #6c757d;
            border: none;
        }
        .carousel-indicators .active {
            background-color: #0d6efd;
        }
        .carousel-caption {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 5px 10px;
            right: 20px;
            left: auto;
            bottom: 20px;
            width: auto;
        }
    </style>
@endpush

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([{{ $boardingHouse->latitude }}, {{ $boardingHouse->longitude }}], 15);
        
            const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                zoomControl: true,
                minZoom: 16,
                maxZoom: 18,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google'
            }).addTo(map);

            const marker = L.marker([{{ $boardingHouse->latitude }}, {{ $boardingHouse->longitude }}])
                .addTo(map)
                .bindPopup('{{ $boardingHouse->name }}')
                .openPopup();

            L.control.scale({imperial: false}).addTo(map);
        });
    </script>
@endpush