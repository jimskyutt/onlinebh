@extends('layouts.app')

@section('title', $house->name . ' | Boarding House Details')

@push('styles')
<style>
    .house-image {
        height: 400px;
        object-fit: cover;
        width: 100%;
        border-radius: 8px;
    }
    .carousel-indicators .active {
        background-color: #0d6efd !important;
    }
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0, 0, 0, 0.5);
        padding: 15px;
        border-radius: 50%;
    }
    .facility-badge {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .related-house-card {
        transition: transform 0.3s;
    }
    .related-house-card:hover {
        transform: translateY(-5px);
    }
    .status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }
</style>
@endpush

@section('content')
<div class="container py-2">
    <div class="mb-2">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Map
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body p-0">
                    @if($house->images->count() > 0)
                        <div id="houseCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($house->images as $key => $image)
                                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             class="d-block w-100" 
                                             alt="{{ $house->name }} - Image {{ $key + 1 }}"
                                             style="height: 400px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if($house->images->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#houseCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#houseCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                                
                                <div class="carousel-indicators" style="position: static; margin: 10px 0 0 0; padding: 0;">
                                    @foreach($house->images as $key => $image)
                                        <button type="button" data-bs-target="#houseCarousel" 
                                                data-bs-slide-to="{{ $key }}" 
                                                class="{{ $key === 0 ? 'active' : '' }}"
                                                style="width: 10px; height: 10px; border-radius: 50%; margin: 0 5px; background-color: #6c757d; border: none;">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-light text-center py-5" style="height: 400px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-images fa-5x text-muted mb-3"></i>
                            <p class="w-100">No images available</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Details</h6>
                    <span class="badge bg-{{ $house->status === 'Available' ? 'success' : 'warning' }} p-2">
                        {{ $house->status }}
                    </span>
                </div>
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ $house->name }}</h4>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><i class="fas fa-map-marker-alt text-primary me-2"></i> {{ $house->address }}</p>
                            <p><i class="fas fa-tag text-primary me-2"></i> 
                                <strong>{{ $house->space_type }}</strong>
                            </p>
                            @if(in_array($house->space_type, ['Bed Space', 'Both']))
                                <p><i class="fas fa-bed text-primary me-2"></i> 
                                    <strong>₱{{ number_format($house->bed_space_price, $house->bed_space_price == floor($house->bed_space_price) ? 0 : 2) }}</strong> / month (Bed Space)
                                </p>
                            @endif
                            @if(in_array($house->space_type, ['Room Space', 'Both']))
                                <p><i class="fas fa-home text-primary me-2"></i> 
                                    <strong>₱{{ number_format($house->room_space_price, $house->room_space_price == floor($house->room_space_price) ? 0 : 2) }}</strong> / month (Room Space)
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-users text-primary me-2"></i> 
                                {{ $house->current_boarders }} / {{ $house->max_boarders }} boarders
                            </p>
                            <p><i class="fas fa-phone text-primary me-2"></i> {{ $house->contact_number }}</p>
                            @if(in_array($house->space_type, ['Bed Space', 'Both']))
                                <p><i class="fas fa-bed text-primary me-2"></i> 
                                    {{ $house->bed_space_per_room }} beds per room
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($house->amenities)
                        <h5 class="mt-4 mb-3">Amenities</h5>
                        <div class="row">
                            @foreach(explode(',', $house->amenities) as $amenity)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <span>{{ trim($amenity) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($house->facilities && $house->facilities->count() > 0)
                        <h5 class="mt-4 mb-3">Facilities</h5>
                        <div class="row">
                            @foreach($house->facilities as $facility)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <span>{{ $facility->facility_name ?? '' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($house->description)
                        <div class="mt-4">
                            <h5>Description</h5>
                            <p class="text-muted">{{ $house->description }}</p>
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
                            <p class="mb-0">{{ $house->contact_person }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-primary me-3">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold">Contact Number</p>
                            <p class="mb-0">{{ $house->contact_number }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-primary me-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold">Address</p>
                            <p class="mb-0">{{ $house->address }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $house->latitude }},{{ $house->longitude }}" 
                           target="_blank" 
                           class="btn btn-outline-primary w-100">
                            <i class="fas fa-directions me-2"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Location</h5>
                </div>
                <div class="card-body p-0" style="height: 300px;">
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([{{ $house->latitude }}, {{ $house->longitude }}], 15);
        
            const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                zoomControl: true,
                minZoom: 16,
                maxZoom: 18,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google'
            }).addTo(map);

            const marker = L.marker([{{ $house->latitude }}, {{ $house->longitude }}])
                .addTo(map)
                .bindPopup('{{ $house->name }}')
                .openPopup();

            L.control.scale({imperial: false}).addTo(map);
});
</script>
@endpush
@endsection
