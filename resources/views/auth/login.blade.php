@extends('layouts.app')

@section('title', 'Login | Online BH Finder')

@section('content')

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-4">
            <div class="card shadow-lg">
                <div class="card-header d-flex flex-column justify-content-center align-items-center py-4" style="background-color: #1e3a8b;">
                    <img src="{{ asset('storage/logo/logo.png') }}" alt="Online BH Finder" class="img-fluid" style="max-width: 100px; height: auto;">
                    <h4 class="text-center text-white fw-bold mt-2 mb-0">Online Boarding House Finder</h4>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('login') }}" method="POST" class="mt-3">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control-sm @error('username') is-invalid @enderror" id="username" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus>
                            <label for="username" class="small">
                                <i class="fas fa-user text-secondary me-1"></i>Username
                            </label>
                            @error('username')
                            <div class="invalid-feedback d-block small">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-4">
                            <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                            <label for="password" class="small">
                                <i class="fas fa-lock text-secondary me-1"></i>Password
                            </label>
                            <button class="btn btn-sm" type="button" id="togglePassword" style="position: absolute; right: 0; top: 0; height: 100%; z-index: 10; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                            @error('password')
                            <div class="invalid-feedback d-block small">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm py-2 fw-bold">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleIcon = document.querySelector('#toggleIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
        });
    });
</script>

@endsection