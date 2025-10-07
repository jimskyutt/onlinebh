<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="icon" href="{{asset('storage/logo/logo.png')}}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>
@include('components.user-status')
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}" style="margin-left:-90px;">
            <img src="{{ asset('storage/logo/logo.png') }}" alt="Online BH Finder" class="navbar-logo me-2">
            <span class="d-none d-sm-inline brand-text" style="font-size: 22px;">Online Boarding House Finder</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item me-1">
                    <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home me-1" style="margin-right: 0.25rem !important;"></i> Home
                    </a>
                </li>
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item me-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.enlistments*') ? 'active' : '' }}" 
                        href="{{ route('admin.enlistments') }}">
                            <i class="fas fa-list-check me-1" style="margin-right: 0.25rem !important;"></i> Enlistments
                        </a>
                    </li>
                    <li class="nav-item me-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.owners*') ? 'active' : '' }}" 
                        href="{{ route('admin.owners.index') }}">
                            <i class="fas fa-history me-1" style="margin-right: 0.25rem !important;"></i> Owners
                        </a>
                    </li>
                    <li class="nav-item me-1">
                        <a class="nav-link d-flex align-items-center {{ request()->routeIs('admin.sessions*') ? 'active' : '' }}" 
                        href="{{ route('admin.sessions') }}">
                            <i class="fas fa-user-shield me-1"></i> Sessions
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center {{ request()->routeIs('account.*') ? 'active' : '' }}" 
                       href="{{ route('account.index') }}">
                        <i class="fas fa-user-circle me-1"></i> Account
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link d-flex align-items-center">
                            <i class="fas fa-sign-out-alt me-1" style="margin-right: 0.25rem !important;"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container mt-4">

        @yield('content')
    </div>  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/user-status.js') }}"></script>
</body>
</html>