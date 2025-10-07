@extends('layouts.admin')

@section('title', 'My Boarding Houses | Online BH Finder')

@section('content')
<div class="container-fluid mb-4" >
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">My Boarding Houses</h1>
        <a href="{{ route('admin.enlistments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add New Boarding House
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
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
                             style="height: 200px; object-fit: cover;">
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
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $boardingHouse->name }}</h5>
                        </div>
                        <p class="card-text text-muted mb-1">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $boardingHouse->address }}
                        </p>
                        <p class="card-text mb-2">
                            <i class="fas fa-user me-1"></i> {{ $boardingHouse->contact_person }}
                        </p>
                        <p class="card-text mb-2">
                            <i class="fas fa-phone me-1"></i> {{ $boardingHouse->contact_number }}
                        </p>
                        @if($boardingHouse->space_type === 'Bed Space')
                        <p class="card-text mb-2">
                            <i class="fas fa-tag me-1"></i>
                            <span>Bed Space: ₱{{ number_format($boardingHouse->bed_space_price, 0, '.', ',') }} / mo</span>
                        </p>
                        @elseif($boardingHouse->space_type === 'Room Space')
                        <p class="card-text mb-2">
                            <i class="fas fa-tag me-1"></i>
                            <span>Room Space: ₱{{ number_format($boardingHouse->room_space_price, 0, '.', ',') }} / mo</span>
                        </p>
                        @elseif($boardingHouse->space_type === 'Both')
                        <p class="card-text mb-2">
                            <i class="fas fa-tag me-1"></i>
                            <span>Price: ₱{{ number_format($boardingHouse->bed_space_price, 0, '.', ',') }} bed / mo</span>
                            <span class="ms-2">₱{{ number_format($boardingHouse->room_space_price, 0, '.', ',') }} room / mo</span>
                        </p>
                        @endif
                        @if($boardingHouse->space_type === 'Bed Space')
                        <p class="card-text mb-3">
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i> 
                                Boarders: {{ $boardingHouse->current_boarders }}/{{ $boardingHouse->max_boarders }} boarders
                            </small>
                        </p>
                        @elseif($boardingHouse->space_type === 'Room Space')
                        <p class="card-text mb-3">
                            <small class="text-muted">
                                <i class="fas fa-door-open me-1"></i> 
                                Rooms: {{ $boardingHouse->available_rooms ?? 0 }}/{{ $boardingHouse->max_rooms ?? 1 }} available
                            </small>
                        </p>
                        @elseif($boardingHouse->space_type === 'Both')
                        <p class="card-text mb-2">
                            <small class="text-muted">
                                <i class="fas fa-users me-1"></i> 
                                Boarders: {{ $boardingHouse->current_boarders }}/{{ $boardingHouse->max_boarders }} boarders
                            </small>
                        </p>
                        <p class="card-text mb-3">
                            <small class="text-muted">
                                <i class="fas fa-door-open me-1"></i> 
                                Rooms: {{ $boardingHouse->available_rooms ?? 0 }}/{{ $boardingHouse->max_rooms ?? 1 }} available
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
                            <form action="{{ route('owner.boarding-houses.destroy', $boardingHouse->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div> 
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-home fa-4x text-muted mb-3"></i>
                    <h4>No boarding houses found</h4>
                    <p class="text-muted">Get started by adding your first boarding house</p>
                    <a href="{{ route('owner.boarding-houses.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Add Boarding House
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($boardingHouses->hasPages())
        <div class="mt-4">
            {{ $boardingHouses->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this boarding house? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let deleteId;
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const confirmDelete = document.getElementById('confirmDelete');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                deleteId = this.getAttribute('data-id');
            });
        });

        confirmDelete.addEventListener('click', function() {
            if (deleteId) {
                document.getElementById('delete-form-' + deleteId).submit();
            }
        });
    });
</script>
@endpush
