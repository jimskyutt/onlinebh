<div class="bh-popup">
    <div class="position-relative">
        @php
            $primaryImage = $bh->images->where('is_primary', true)->first() ?? $bh->images->first();
        @endphp
        @if($primaryImage && $primaryImage->image_path)
        <div class="bh-popup-image mb-2">
            <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $bh->name }}" class="img-fluid rounded">
        </div>
        @elseif($bh->bh_image)
        <div class="bh-popup-image mb-2">
            <img src="{{ asset('storage/' . $bh->bh_image) }}" alt="{{ $bh->name }}" class="img-fluid rounded">
        </div>
        @else
        <div class="bh-popup-image mb-2 bg-light text-center p-3 rounded">
            <i class="fas fa-home fa-3x text-muted"></i>
            <div class="small mt-1">No Image Available</div>
        </div>
        @endif
        <span class="position-absolute top-0 end-0 m-2 badge bg-{{ 
            $bh->status === 'Available' ? 'success' : 
            ($bh->status === 'Under Maintenance' ? 'warning' : 'danger') 
        }}" style="z-index: 1;">
            {{ $bh->status }}
        </span>
    </div>
    
    <div class="bh-popup-header">
        <h6 class="mb-1 fw-bold">{{ $bh->name }}</h6>
    </div>
    
    <div class="bh-popup-body">
        <div class="mb-2">
            <i class="fas fa-user me-2 text-primary"></i>
            <span>{{ $bh->contact_person }}</span>
        </div>
        <div class="mb-2">
            <i class="fas fa-phone me-2 text-primary"></i>
            <a href="tel:{{ $bh->contact_number }}" class="text-decoration-none">{{ $bh->contact_number }}</a>
        </div>
        @if($bh->space_type === 'Bed Space' || $bh->space_type === 'Both')
        <div class="mb-2">
            <i class="fas fa-users me-2 text-primary"></i>
            <span>Boarders: {{ $bh->current_boarders }}/{{ $bh->max_boarders }} boarders</span>
        </div>
        @endif
        @if($bh->space_type === 'Room Space' || $bh->space_type === 'Both')
        <div class="mb-2">
            <i class="fas fa-door-open me-2 text-primary"></i>
            <span>Rooms: {{ $bh->available_rooms ?? 0 }}/{{ $bh->max_rooms ?? 1 }} room{{ $bh->max_rooms != 1 ? 's' : '' }} available</span>
        </div>
        @endif
        @if($bh->space_type === 'Bed Space')
        <div class="mb-2">
            <i class="fas fa-tag me-2 text-primary"></i>
            <span>Bed Space: ₱{{ number_format($bh->bed_space_price, 0, '.', ',') }} / mo</span>
        </div>
        @elseif($bh->space_type === 'Room Space')
        <div class="mb-2">
            <i class="fas fa-tag me-2 text-primary"></i>
            <span>Room Space: ₱{{ number_format($bh->room_space_price, 0, '.', ',') }} /mo</span>
        </div>
        @elseif($bh->space_type === 'Both')
        <div class="mb-2">
            <i class="fas fa-tag me-2 text-primary"></i>
            <span>Price: ₱{{ number_format($bh->bed_space_price, 0, '.', ',') }} bed / mo  ₱{{ number_format($bh->room_space_price, 0, '.', ',') }} room / mo</span>
        </div>
        @else
        <div class="mb-2">
            <i class="fas fa-tag me-2 text-primary"></i>
            <span>₱{{ number_format($bh->price, 0, '.', ',') }} / mo</span>
        </div>
        @endif
    </div>
    
    <div class="bh-popup-footer d-flex justify-content-between pt-2 border-top">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $bh->latitude }},{{ $bh->longitude }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary"
           title="Get Directions">
            <i class="fas fa-directions"></i> Directions
        </a>
        <a href="{{ route('admin.enlistments.show', ['boardingHouse' => $bh->id, 'from' => 'map']) }}" 
           class="btn btn-sm btn-bh text-white"
           title="View Details">
            <i class="fas fa-eye"></i> View
        </a>
    </div>
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
