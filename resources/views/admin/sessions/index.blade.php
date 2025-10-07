@extends('layouts.admin')

@section('title', 'Active User Sessions')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">User Sessions</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="sessionsTable" width="100%" cellspacing="0">
                    <thead class="text-center">
                        <tr>
                            <th width=40%>User</th>
                            <th width="10%">Role</th>
                            <th width="30%">Last Activity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($sessions as $session)
                            @php
                                $user = $session->user;
                                $isUserActive = $user && $user->exists;
                                
                                $isLoggedOut = !is_null($session->logged_out_at);
                                
                                $isActive = $isUserActive && (
                                    (!$isLoggedOut && \Carbon\Carbon::parse($session->last_activity)->diffInMinutes(now()) < 5) ||
                                    ($isLoggedOut && 
                                     \Carbon\Carbon::parse($session->last_activity) > \Carbon\Carbon::parse($session->logged_out_at) &&
                                     \Carbon\Carbon::parse($session->last_activity)->diffInMinutes(now()) < 5
                                    )
                                );
                                
                                $displayName = 'Deleted User';
                                $roleBadge = 'secondary';
                                $roleLabel = 'N/A';
                                
                                if ($isUserActive) {
                                    if ($user->role === 'admin') {
                                        $displayName = 'Admin';
                                        $roleBadge = 'primary';
                                        $roleLabel = 'Admin';
                                    } else if ($user->role === 'owner') {
                                        $boardingHouse = $user->boardingHouses->first();
                                        $displayName = $boardingHouse ? $boardingHouse->contact_person : 'Owner';
                                        $roleBadge = 'success';
                                        $roleLabel = 'Owner';
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    {{ $displayName }}
                                </td>
                                <td>
                                    @if($isUserActive)
                                        <span class="badge bg-{{ $roleBadge }}">
                                            {{ $roleLabel }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td title="{{ $session->last_activity }}" class="d-flex align-items-center justify-content-center gap-5">
                                    @php
                                        $lastActivity = \Carbon\Carbon::parse($session->last_activity);
                                        echo $lastActivity->format('m/d/y H:i');
                                    @endphp
                                    <small class="d-block text-muted">{{ $session->last_activity_diff }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $isActive ? 'bg-success' : 'bg-warning' }}">
                                        {{ $isActive ? 'Active Now' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No active sessions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.35em 0.65em;
    }
    .bg-primary {
        background-color: #4e73df !important;
    }
    .bg-success {
        background-color: #1cc88a !important;
    }
    .bg-warning {
        background-color: #f6c23e !important;
        color: #000 !important;
    }
</style>
@endpush
