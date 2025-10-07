@extends('layouts.admin')

@section('title', 'Account Settings | Online BH Finder')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Account Settings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Username</h6>
                </div>
                <div class="card-body">
                    <form id="updateUsernameForm">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">New Username</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="{{ auth()->user()->username }}" required>
                                <button class="btn btn-primary" type="submit" id="updateUsernameBtn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Update Username
                                </button>
                            </div>
                            <div id="usernameFeedback" class="valid-feedback"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                </div>
                <div class="card-body">
                    <form id="updatePasswordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <button class="btn btn-secondary toggle-password" data-target="current_password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <button class="btn btn-secondary toggle-password" data-target="new_password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                <button class="btn btn-secondary toggle-password" data-target="new_password_confirmation" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="updatePasswordBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        const updateUsernameUrl = '{{ route("account.update-username") }}';
        const updatePasswordUrl = '{{ route("account.update-password") }}';
    </script>
    @vite(['resources/js/account.js'])
@endpush