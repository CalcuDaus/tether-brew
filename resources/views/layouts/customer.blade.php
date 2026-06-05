<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tether Brew App')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine & Swal -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Leaflet JS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            background-color: #f1f5f9; /* Grey background for desktop view */
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        /* Mobile App Wrapper */
        .app-wrapper {
            background-color: #ffffff;
            width: 100%;
            max-width: 480px; /* Mobile width constraint on desktop */
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Content area (scrollable) */
        .app-content {
            flex: 1;
            padding-bottom: 70px; /* Space for bottom nav */
            overflow-y: auto;
        }

        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            height: 65px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 50;
            padding-bottom: env(safe-area-inset-bottom); /* iPhone X support */
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            gap: 4px;
            transition: all 0.2s;
            width: 25%;
        }

        .nav-item.active {
            color: #8b5c2a; /* Coffee color */
        }

        .nav-item svg {
            width: 24px;
            height: 24px;
            stroke-width: 2;
        }

        .nav-item.active svg {
            fill: rgba(139, 92, 42, 0.1);
        }

        /* Top Header Area (Shared) */
        .app-header {
            padding: 20px;
            background: #ffffff;
            position: sticky;
            top: 0;
            z-index: 40;
        }

    </style>
    @stack('styles')
</head>
<body>

    <div class="app-wrapper">
        <div class="app-content">
            @yield('content')
        </div>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="{{ route('customer.dashboard') }}" class="nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Beranda</span>
            </a>
            
            <a href="{{ route('customer.menu') }}" class="nav-item {{ request()->routeIs('customer.menu') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
                <span>Menu</span>
            </a>
            
            <a href="{{ route('customer.chats') }}" class="nav-item {{ request()->routeIs('customer.chats') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Chats</span>
            </a>
            
            <a href="{{ route('customer.me') }}" class="nav-item {{ request()->routeIs('customer.me') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Me</span>
            </a>
        </nav>
    </div>

    <!-- SweetAlert Toast Logic -->
    @if(session('success') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });

                @if(session('success'))
                    Toast.fire({ icon: 'success', title: {!! json_encode(session('success')) !!} });
                @endif
                @if($errors->any())
                    Toast.fire({ icon: 'error', title: {!! json_encode($errors->first()) !!} });
                @endif
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>
