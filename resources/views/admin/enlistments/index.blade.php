@extends('layouts.admin')

@section('title' , 'Enlistments | Online BH Finder')

@section('content')
<div class="container-fluid mb-4" >
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Boarding House Enlistments</h1>
        <a href="{{ route('admin.enlistments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Boarding House
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3 g-4">
        @forelse($boardingHouses as $boardingHouse)
            <div class="col">
                <div class="card h-100 shadow-sm position-relative">
                    @php
                        $primaryImage = $boardingHouse->images->where('is_primary', true)->first() ?? $boardingHouse->images->first();
                    @endphp
                    @if($primaryImage)
                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" 
                             class="card-img-top" 
                             alt="{{ $boardingHouse->name }}"
                             style="height: 200px; object-fit: cover; width: 100%;">
                    @elseif($boardingHouse->bh_image)
                        <img src="{{ asset('storage/' . $boardingHouse->bh_image) }}" 
                             class="card-img-top" 
                             alt="{{ $boardingHouse->name }}"
                             style="height: 200px; object-fit: cover; width: 100%;">
                    @else
                        <div class="bg-light text-center py-5" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-home fa-4x text-muted"></i>
                        </div>
                    @endif

                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-{{ $boardingHouse->status === 'Available' ? 'success' : 'warning' }} p-2">
                            {{ $boardingHouse->status }}
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $boardingHouse->name }}</h5>
                        <p class="card-text text-muted mb-1">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $boardingHouse->address }}
                        </p>
                        <p class="card-text mb-2">
                            <i class="fas fa-user me-1"></i> {{ $boardingHouse->contact_person }}
                        </p>
                        <p class="card-text mb-2">
                            <i class="fas fa-phone me-1"></i> {{ $boardingHouse->contact_number }}
                        </p>
                        <p class="card-text mb-2">
                            @if($boardingHouse->space_type === 'Bed Space')
                                <strong>Bed Space: ₱{{ number_format($boardingHouse->bed_space_price, $boardingHouse->bed_space_price == floor($boardingHouse->bed_space_price) ? 0 : 2) }}</strong> / mo
                            @elseif($boardingHouse->space_type === 'Room Space')
                                <strong>Room Space: ₱{{ number_format($boardingHouse->room_space_price, $boardingHouse->room_space_price == floor($boardingHouse->room_space_price) ? 0 : 2) }}</strong> / mo
                            @elseif($boardingHouse->space_type === 'Both')
                                <strong>Price: ₱{{ number_format($boardingHouse->bed_space_price, $boardingHouse->bed_space_price == floor($boardingHouse->bed_space_price) ? 0 : 2) }} bed / mo 
                                ₱{{ number_format($boardingHouse->room_space_price, $boardingHouse->room_space_price == floor($boardingHouse->room_space_price) ? 0 : 2) }} room / mo</strong>
                            @else
                                <strong>₱{{ number_format($boardingHouse->price, $boardingHouse->price == floor($boardingHouse->price) ? 0 : 2) }}</strong> / mo
                            @endif
                        </p>
                        @if($boardingHouse->space_type === 'Bed Space' || $boardingHouse->space_type === 'Both')
                            <p class="card-text mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i> 
                                    {{ $boardingHouse->current_boarders }} / {{ $boardingHouse->max_boarders }} boarders
                                </small>
                            </p>
                        @endif
                        @if($boardingHouse->space_type === 'Room Space' || $boardingHouse->space_type === 'Both')
                            <p class="card-text mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-door-open me-1"></i> 
                                    {{ $boardingHouse->available_rooms }} / {{ $boardingHouse->max_rooms }} rooms available
                                </small>
                            </p>
                        @endif
                    </div>
                    <div class="card-footer" style="background-color:rgb(228, 228, 228)">
                        <div class="d-flex justify-content-center gap-5 align-items-center">
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $boardingHouse->latitude }},{{ $boardingHouse->longitude }}&travelmode=driving" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-directions"></i>
                            </a>
                            <a href="{{ route('admin.enlistments.show', $boardingHouse->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" style="width: 45px; height: 35px;">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form action="{{ route('admin.enlistments.destroy', $boardingHouse->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div> 
                </div>
            </div>
        @empty

        @endforelse
    </div>

    @if($boardingHouses->isEmpty())
        <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
            <div class="text-center">
                <i class="fas fa-home fa-4x text-muted mb-3"></i>
                <h4>No boarding houses found</h4>
                <p class="text-muted">Get started by adding a new boarding house</p>
            </div>
        </div>
    @endif

    @if($boardingHouses->hasPages())
        <div class="mt-4">
            {{ $boardingHouses->links() }}
        </div>
    @endif
</div>
@include('partials.del-conf')
@push('scripts')
<script>
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