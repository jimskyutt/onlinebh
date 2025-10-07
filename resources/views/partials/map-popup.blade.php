<div class="position-relative">
    @php
        $primaryImage = $house->images->where('is_primary', true)->first() ?? $house->images->first();
    @endphp
    @if($primaryImage)
    <div class="bh-popup-image mb-2" style="height: 150px; overflow: hidden; width : 200px;">
        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" 
             alt="{{ $house->name }}" 
             class="img-fluid rounded w-100"
             style="height: 100%; object-fit: cover;">
    </div>
    @elseif($house->bh_image)
    <div class="bh-popup-image mb-2" style="height: 150px; overflow: hidden;">
        <img src="{{ asset('storage/' . $house->bh_image) }}" 
             alt="{{ $house->name }}" 
             class="img-fluid rounded w-100"
             style="height: 100%; object-fit: cover;">
    </div>
    @else
    <div class="bh-popup-image mb-2 bg-light text-center p-3 rounded" style="height: 150px; display: flex; flex-direction: column; justify-content: center;">
        <i class="fas fa-home fa-3x text-muted"></i>
        <div class="small mt-1">No Image Available</div>
    </div>
    @endif
    <span class="position-absolute top-0 end-0 m-2 badge bg-{{ 
        $house->status === 'Available' ? 'success' : 
        ($house->status === 'Under Maintenance' ? 'warning' : 'danger') 
    }}" style="z-index: 1;">
        {{ $house->status }}
    </span>
</div>

<div class="bh-popup-header">
    <h6 class="mb-1 fw-bold">{{ $house->name }}</h6>
    <div class="text-muted small mb-2">
        <i class="fas fa-map-marker-alt me-1"></i> {{ $house->address }}
    </div>
</div>

<div class="bh-popup-body">
    <div class="mb-2">
        <i class="fas fa-user me-2 text-primary"></i>
        <span>{{ $house->contact_person }}</span>
    </div>
    <div class="mb-2">
        <i class="fas fa-phone me-2 text-primary"></i>
        <a href="tel:{{ $house->contact_number }}" class="text-decoration-none">{{ $house->contact_number }}</a>
    </div>
    @if($house->space_type === 'Bed Space')
    <div class="mb-2">
        <i class="fas fa-users me-2 text-primary"></i>
        <span>Boarders: {{ $house->current_boarders }}/{{ $house->max_boarders }} boarders</span>
    </div>
    @elseif($house->space_type === 'Room Space')
    <div class="mb-2">
        <i class="fas fa-door-open me-2 text-primary"></i>
        <span>Rooms: {{ $house->available_rooms ?? 0 }}/{{ $house->max_rooms ?? 1 }} available</span>
    </div>
    @elseif($house->space_type === 'Both')
    <div class="mb-2">
        <i class="fas fa-users me-2 text-primary"></i>
        <span>Boarders: {{ $house->current_boarders }}/{{ $house->max_boarders }} boarders</span>
    </div>
    <div class="mb-2">
        <i class="fas fa-door-open me-2 text-primary"></i>
        <span>Rooms: {{ $house->available_rooms ?? 0 }}/{{ $house->max_rooms ?? 1 }} available</span>
    </div>
    @endif
    @if($house->space_type === 'Bed Space')
    <div class="mb-2">
        <i class="fas fa-tag me-2 text-primary"></i>
        <span>Bed Space: ₱{{ number_format($house->bed_space_price, 0, '.', ',') }} / mo</span>
    </div>
    @elseif($house->space_type === 'Room Space')
    <div class="mb-2">
        <i class="fas fa-tag me-2 text-primary"></i>
        <span>Room Space: ₱{{ number_format($house->room_space_price, 0, '.', ',') }} / mo</span>
    </div>
    @elseif($house->space_type === 'Both')
    <div class="mb-2">
        <i class="fas fa-tag me-2 text-primary"></i>
        <span>Price: ₱{{ number_format($house->bed_space_price, 0, '.', ',') }} bed / mo ₱{{ number_format($house->room_space_price, 0, '.', ',') }} room / mo</span>
    </div>
    @endif
    @if($house->images->count() > 1)
    <div class="small text-muted">
        <i class="fas fa-images me-1"></i> +{{ $house->images->count() - 1 }} more {{ Str::plural('image', $house->images->count() - 1) }}
    </div>
    @endif
</div>

<div class="bh-popup-footer d-flex justify-content-between pt-2 border-top">
    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $house->latitude }},{{ $house->longitude }}" 
       target="_blank" 
       class="btn btn-sm btn-outline-primary"
       title="Get Directions">
        <i class="fas fa-directions"></i> Directions
    </a>
    <a href="{{ route('public.boarding-houses.show', $house) }}" 
       class="btn btn-sm btn-bh text-white"
       title="View Details">
        <i class="fas fa-eye"></i> View
    </a>
</div>

<style>
.bh-popup {
    min-width: 250px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.bh-popup-image {
    width: 100%;
    height: 120px;
    overflow: hidden;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.bh-popup-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bh-popup-header {
    margin: 0.5rem 0;
}

.bh-popup-header h6 {
    margin: 0;
    font-size: 1rem;
    line-height: 1.2;
}

.bh-popup-body {
    font-size: 0.875rem;
}

.bh-popup-body i {
    width: 16px;
    text-align: center;
}

.bh-popup-footer {
    margin-top: 0.5rem;
}

.bh-popup-footer .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>