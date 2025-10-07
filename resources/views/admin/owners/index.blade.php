@extends('layouts.admin')

@section('title', 'Manage Owners | Online BH Finder')

@section('content')

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Manage Owners</h1>
        <a href="{{ route('admin.owners.create') }}" class="btn btn-bh">
            <i class="fas fa-plus me-1"></i> Add New Owner
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%">Boarding Houses</th>
                            <th style="width: 20%; text-align: center;">Contact Person</th>
                            <th style="width: 20%; text-align: center;">Contact Number</th>
                            <th style="width: 15%; text-align: center;">Username</th>
                            <th style="width: 20%; text-align: center;">Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($owners as $owner)
                            <tr>
                                <td class="align-top">
                                    @if($owner->boardingHouses->isNotEmpty())
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($owner->boardingHouses as $bh)
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2">{{ $bh->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small">No boarding houses</span>
                                    @endif
                                </td>
                                <td class="align-top text-center">
                                    @if($owner->boardingHouses->isNotEmpty())
                                        <div class="text-nowrap">{{ $owner->boardingHouses->first()->contact_person ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="align-top text-center">
                                    @if($owner->boardingHouses->isNotEmpty())
                                        <div class="text-nowrap">{{ $owner->boardingHouses->first()->contact_number ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                
                                <td class="align-top text-center">
                                    <div class="editable" 
                                         data-field="username" 
                                         data-user-id="{{ $owner->user_id }}" 
                                         data-original-value="{{ $owner->username }}">
                                        {{ $owner->username }}
                                    </div>
                                </td>
                                
                                <td class="align-top text-center">
                                    <div class="editable-password" 
                                         data-field="password" 
                                         data-user-id="{{ $owner->user_id }}" 
                                         data-original-value=""
                                         data-original-display="{{ $owner->password ? '*************' : 'N/A' }}">
                                        {{ $owner->password ? '*************' : 'N/A' }}
                                    </div>
                                </td>
                                
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">No owners found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($owners->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $owners->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
    @vite(['resources/js/owners.js'])
@endpush
@endsection


