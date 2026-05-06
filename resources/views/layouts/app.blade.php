<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Tether Brew</title>
    @if(auth()->check() && auth()->user()->isRider())
        <link rel="manifest" href="/manifest-rider.json">
        <meta name="theme-color" content="#1e293b">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="TB Rider">
        <link rel="apple-touch-icon" href="/icons/rider-192x192.png">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'light') {
                document.documentElement.classList.add('light-theme');
            }
        })();

        window.switchTheme = async (event, callback) => {
            const x = event.clientX;
            const y = event.clientY;
            const endRadius = Math.hypot(
                Math.max(x, innerWidth - x),
                Math.max(y, innerHeight - y)
            );

            if (!document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                if (callback) callback();
                document.documentElement.classList.toggle('light-theme');
                const newTheme = document.documentElement.classList.contains('light-theme') ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
                return;
            }

            const transition = document.startViewTransition(() => {
                if (callback) callback();
                document.documentElement.classList.toggle('light-theme');
                const newTheme = document.documentElement.classList.contains('light-theme') ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
            });

            await transition.ready;

            document.documentElement.animate(
                {
                    clipPath: [
                        `circle(0px at ${x}px ${y}px)`,
                        `circle(${endRadius}px at ${x}px ${y}px)`
                    ]
                },
                {
                    duration: 500,
                    easing: 'ease-in-out',
                    pseudoElement: '::view-transition-new(root)'
                }
            );
        };
    </script>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false, darkMode: (localStorage.getItem('theme') || 'light') !== 'light' }" class="{{ auth()->check() && auth()->user()->isRider() ? 'is-rider' : '' }}">

    {{-- Sidebar --}}
    <aside class="sidebar" :class="{ 'open': sidebarOpen }" @click.away="sidebarOpen = false">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                </svg>
            </div>
            <div>
                <div class="sidebar-brand-text">Tether Brew</div>
                <div class="sidebar-brand-sub">Management System</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                <div class="nav-section">
                    <div class="nav-section-title">Menu Utama</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>
                            </svg>
                        </span> Dashboard
                    </a>
                    @if(auth()->user()->isOwner())
                    <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </span> Akun
                    </a>
                    @endif
                    <a href="{{ route('carts.index') }}" class="nav-link {{ request()->routeIs('carts.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                            </svg>
                        </span> Gerobak
                    </a>
                    <a href="{{ route('riders.index') }}" class="nav-link {{ request()->routeIs('riders.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span> Rider
                    </a>
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                            </svg>
                        </span> Produk
                    </a>
                    <a href="{{ route('inventories.index') }}" class="nav-link {{ request()->routeIs('inventories.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                            </svg>
                        </span> Stok
                    </a>
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="8"/><line x1="12" x2="12" y1="16" y2="8"/><line x1="10" x2="14" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="14"/>
                            </svg>
                        </span> Transaksi
                    </a>
                    <a href="{{ route('admin.artikel.index') }}" class="nav-link {{ request()->routeIs('admin.artikel.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </span> Artikel
                    </a>
                </div>
            @endif

            @if(auth()->user()->isRider())
                <div class="nav-section">
                    <div class="nav-section-title">Menu Rider</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-link-icon"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg></span> Dashboard
                    </a>
                    <a href="{{ route('rider.pos') }}" class="nav-link {{ request()->routeIs('rider.pos') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
                            </svg>
                        </span> POS
                    </a>
                    <a href="{{ route('rider.transactions') }}" class="nav-link {{ request()->routeIs('rider.transactions') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            </svg>
                        </span> Riwayat
                    </a>
                    <a href="{{ route('rider.chat.index') }}" class="nav-link {{ request()->routeIs('rider.chat.*') ? 'active' : '' }}">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </span> Chat
                        @if(isset($unreadChatCount) && $unreadChatCount > 0)
                            <span style="background: var(--accent); color: white; font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.45rem; border-radius: 10px; margin-left: auto;">{{ $unreadChatCount }}</span>
                        @endif
                    </a>
                </div>
            @endif

            <div class="nav-section">
                <div class="nav-section-title">Lainnya</div>
                <a href="{{ route('landing') }}" class="nav-link">
                    <span class="nav-link-icon">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/>
                        </svg>
                    </span> Landing Page
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2-custom">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm w-full-custom flex-center">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                    </svg> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="main-content">
        <header class="topbar">
            <div class="flex-gap-3">
                <button class="mobile-menu-btn" @click.stop="sidebarOpen = !sidebarOpen">☰</button>
                <h1 class="topbar-title">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="topbar-actions">
                @if(auth()->check() && auth()->user()->isRider())
                    <button id="pwa-install-btn-rider" style="display:none;" onclick="installRiderPWA()" class="btn btn-secondary btn-sm flex-center" style="width: 40px; height: 40px; padding: 0; border-radius: 50%;" title="Install App">
                        <svg width="1.4em" height="1.4em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                    </button>
                @endif
                <button @click="window.switchTheme($event, () => darkMode = !darkMode)" class="btn btn-secondary btn-sm flex-center" style="width: 40px; height: 40px; padding: 0; border-radius: 50%;">
                    <template x-if="darkMode">
                        <svg class="icon-two-tone" width="1.4em" height="1.4em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="5"/><line x1="12" x2="12" y1="1" y2="3"/><line x1="12" x2="12" y1="21" y2="23"/><line x1="4.22" x2="5.64" y1="4.22" y2="5.64"/><line x1="18.36" x2="19.78" y1="18.36" y2="19.78"/><line x1="1" x2="3" y1="12" y2="12"/><line x1="21" x2="23" y1="12" y2="12"/><line x1="4.22" x2="5.64" y1="19.78" y2="18.36"/><line x1="18.36" x2="19.78" y1="5.64" y2="4.22"/>
                        </svg>
                    </template>
                    <template x-if="!darkMode">
                        <svg class="icon-two-tone" width="1.4em" height="1.4em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </template>
                </button>
                @yield('actions')
            </div>
        </header>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> {{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    ❌ {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableContainers = document.querySelectorAll('.table-container');
            
            tableContainers.forEach(container => {
                const table = container.querySelector('table');
                if (!table) return;

                // Membungkus input search dengan form untuk pencarian server-side
                const searchForm = document.createElement('form');
                searchForm.action = window.location.pathname; // Submit ke halaman saat ini
                searchForm.method = 'GET';
                searchForm.style.display = 'flex';
                searchForm.style.justifyContent = 'flex-end';
                searchForm.style.marginBottom = '1rem';
                
                // Menjaga parameter query lain selain page dan search
                const currentUrlParams = new URLSearchParams(window.location.search);
                currentUrlParams.delete('page'); // Reset pagination saat mencari
                currentUrlParams.delete('search');

                currentUrlParams.forEach((value, key) => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = key;
                    hiddenInput.value = value;
                    searchForm.appendChild(hiddenInput);
                });

                // Membuat element input
                const searchInput = document.createElement('input');
                searchInput.setAttribute('type', 'text');
                searchInput.setAttribute('name', 'search');
                
                // Isi input dengan parameter pencarian saat ini jika ada
                const currentSearch = new URLSearchParams(window.location.search).get('search');
                if (currentSearch) {
                    searchInput.value = currentSearch;
                }

                searchInput.setAttribute('placeholder', 'Ketik & Tekan Enter untuk mencari...');
                searchInput.className = 'form-input'; 
                searchInput.style.maxWidth = '300px';

                searchForm.appendChild(searchInput);
                
                // Menyisipkan form sebelum elemen container tabel
                container.parentNode.insertBefore(searchForm, container);

                // Tambahkan filter sisi klien agar instan untuk data di halaman saat ini
                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const rows = tbody.querySelectorAll('tr');

                searchInput.addEventListener('keyup', function(e) {
                    const term = e.target.value.toLowerCase();
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(term)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
    @if(auth()->check() && auth()->user()->isRider())
    {{-- ===== BOTTOM NAV MOBILE (RIDER) ===== --}}
    <nav class="bottom-nav">
        <div class="bottom-nav-inner">
            <a href="{{ route('dashboard') }}" class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <span>Beranda</span>
            </a>
            <a href="{{ route('rider.pos') }}" class="bottom-nav-item {{ request()->routeIs('rider.pos') ? 'active' : '' }}">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>POS</span>
            </a>
            <a href="{{ route('rider.transactions') }}" class="bottom-nav-item {{ request()->routeIs('rider.transactions') ? 'active' : '' }}">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                <span>Riwayat</span>
            </a>
            <a href="{{ route('rider.chat.index') }}" class="bottom-nav-item {{ request()->routeIs('rider.chat.*') ? 'active' : '' }}" style="position:relative;">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>Chat</span>
                @if(isset($unreadChatCount) && $unreadChatCount > 0)
                    <span style="position:absolute; top:-5px; right:15px; background:var(--accent-red); color:white; font-size:10px; border-radius:10px; padding:2px 6px;">{{ $unreadChatCount }}</span>
                @endif
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('rider-account-modal').style.display='flex'" class="bottom-nav-item">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Akun</span>
            </a>
        </div>
    </nav>

    {{-- ===== ACCOUNT MODAL ===== --}}
    <div id="rider-account-modal" class="account-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:var(--bg-card); padding:24px; border-radius:16px; width:90%; max-width:400px; text-align:center; border: 1px solid var(--border-color);">
            <div style="width:64px; height:64px; border-radius:50%; background:var(--gradient-gold); margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; color:#fff;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <h3 style="margin-bottom:4px; font-size: 1.2rem; color: var(--text-primary);">{{ auth()->user()->name }}</h3>
            <p style="margin:0 0 20px 0; color:var(--text-secondary); font-size:14px; text-transform: capitalize;">{{ auth()->user()->role }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger" style="width:100%; justify-content:center;">Keluar</button>
            </form>
            <button onclick="document.getElementById('rider-account-modal').style.display='none'" class="btn btn-secondary" style="width:100%; justify-content:center; margin-top:12px;">Batal</button>
        </div>
    </div>
    @endif

    @if(auth()->check() && auth()->user()->isRider())
    <script>
    // Register Rider Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw-rider.js', { scope: '/' })
                .then(reg => console.log('Rider SW registered:', reg.scope))
                .catch(err => console.log('Rider SW failed:', err));
        });
    }

    // Custom Install Prompt for Rider
    let riderDeferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        riderDeferredPrompt = e;

        // Tampilkan tombol di topbar
        const topbarBtn = document.getElementById('pwa-install-btn-rider');
        if (topbarBtn) topbarBtn.style.display = 'flex';

        setTimeout(() => {
            if (!riderDeferredPrompt) return;
            const banner = document.createElement('div');
            banner.id = 'pwa-rider-install';
            banner.innerHTML = `
                <div style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#1e293b;color:white;padding:12px 20px;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.4);z-index:99999;display:flex;align-items:center;justify-content:space-between;width:calc(100% - 40px);max-width:400px;font-family:Inter,sans-serif;border:1px solid rgba(255,255,255,0.1);animation:riderSlideUp .4s ease;">
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <span style="font-weight:700;font-size:14px;line-height:1.2;">Install TB Rider</span>
                        <span style="font-size:12px;opacity:0.8;line-height:1.2;">Akses POS lebih cepat</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                        <button onclick="installRiderPWA()" style="background:#22c55e;color:white;border:none;padding:6px 14px;border-radius:8px;font-weight:700;font-size:13px;cursor:pointer;">Install</button>
                        <button onclick="this.closest('#pwa-rider-install').remove()" style="background:none;border:none;color:white;font-size:20px;cursor:pointer;padding:0;line-height:1;opacity:0.6;">&times;</button>
                    </div>
                </div>
            `;
            document.body.appendChild(banner);
        }, 10000);
    });

    async function installRiderPWA() {
        if (!riderDeferredPrompt) return;
        riderDeferredPrompt.prompt();
        const { outcome } = await riderDeferredPrompt.userChoice;
        riderDeferredPrompt = null;
        
        const topbarBtn = document.getElementById('pwa-install-btn-rider');
        if (topbarBtn) topbarBtn.style.display = 'none';
        
        const banner = document.getElementById('pwa-rider-install');
        if (banner) banner.remove();
    }

    window.addEventListener('appinstalled', (evt) => {
        const topbarBtn = document.getElementById('pwa-install-btn-rider');
        if (topbarBtn) topbarBtn.style.display = 'none';
        const banner = document.getElementById('pwa-rider-install');
        if (banner) banner.remove();
        console.log('TB Rider was installed');
    });
    </script>
    <style>
    @keyframes riderSlideUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
    </style>
    @endif

    @stack('scripts')
</body>
</html>

