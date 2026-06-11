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
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* SweetAlert Custom UI to match Tether Brew theme */
        .swal2-popup { border-radius: 12px !important; font-family: 'Inter', sans-serif !important; }
        .light-theme .swal2-popup { background: var(--bg-card) !important; color: var(--text-primary) !important; }
        .swal2-title { font-size: 1.25rem !important; }
        .swal2-styled.swal2-confirm { background-color: var(--accent) !important; border-radius: 8px !important; }
        .swal2-styled.swal2-cancel { background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; border-radius: 8px !important; }
    </style>
    <script>
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-confirm')) {
                e.preventDefault();
                const message = form.getAttribute('data-confirm');
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.removeAttribute('data-confirm');
                        form.submit();
                    }
                });
            }
        });
    </script>
    @stack('styles')
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
    <style>
        /* Custom Select2 Theme Integration */
        .select2-container--default .select2-selection--single {
            background-color: var(--bg-card, #fff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 8px;
            height: 42px;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-primary, #1e293b);
            line-height: normal;
            padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 10px;
        }
        .select2-dropdown {
            background-color: var(--bg-card, #fff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .select2-search--dropdown .select2-search__field {
            background-color: var(--bg-main, #f8fafc);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 6px;
            color: var(--text-primary, #1e293b);
        }
        .select2-container--default .select2-results__option {
            color: var(--text-primary, #1e293b);
            padding: 8px 12px;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--accent, #3b82f6);
            color: white;
        }
        .select2-container--default .select2-results__option--selected {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--accent, #3b82f6);
        }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false, desktopSidebarCollapsed: (localStorage.getItem('desktopSidebarCollapsed') === 'true'), darkMode: (localStorage.getItem('theme') || 'light') !== 'light' }" class="{{ auth()->check() && auth()->user()->isRider() ? 'is-rider' : '' }}" :class="{ 'sidebar-collapsed': desktopSidebarCollapsed }">

    {{-- Sidebar --}}
    <aside class="sidebar" :class="{ 'open': sidebarOpen }" @click.away="sidebarOpen = false">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <img src="{{ asset('tether-icon-head.webp') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div>
                <div class="sidebar-brand-text">Tether Brew</div>
                <div class="sidebar-brand-sub">Management System</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->user()->isOwner())
                {{-- ====== OWNER SIDEBAR ====== --}}
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('owner.rider_performance.*') ? 'active' : '' }}">
                    <span class="nav-link-icon">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                    </span> Dashboard
                </a>

                @php $isLaporanActive = request()->routeIs(['admin.journals.*', 'admin.rider_sales_report.*', 'admin.rider_minus.*', 'admin.payroll.*', 'admin.rider_finances.kasbon', 'admin.office_kasbon.*']); @endphp
                <div class="nav-section" x-data="{ open: {{ $isLaporanActive ? 'true' : 'false' }} }">
                    <div class="nav-link dropdown-toggle" @click="open = !open" :class="{ 'active': {{ $isLaporanActive ? 'true' : 'false' }} }">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/>
                            </svg>
                        </span>
                        <span>Laporan</span>
                        <svg class="dropdown-arrow" :class="{ 'rotated': open }" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <div x-show="open" x-collapse x-transition.opacity.duration.300ms class="nav-submenu">
                        <a href="{{ route('admin.journals.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.journals.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon"><svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 7h6"/><path d="M8 11h8"/></svg></span>
                            Jurnal Umum
                        </a>
                        <a href="{{ route('admin.rider_finances.kasbon') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_finances.kasbon') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </span>
                            Kasbon Rider
                        </a>
                        <a href="{{ route('admin.office_kasbon.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.office_kasbon.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            </span>
                            Kasbon Office
                        </a>
                        <a href="{{ route('admin.rider_sales_report.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_sales_report.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon"><svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg></span>
                            Laporan Penjualan
                        </a>
                        <a href="{{ route('admin.rider_minus.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_minus.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon"><svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg></span>
                            Laporan Minus
                        </a>
                        <a href="{{ route('admin.payroll.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.payroll.index', 'admin.payroll.show') ? 'active' : '' }}">
                            <span class="nav-submenu-icon"><svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg></span>
                            Slip Gaji Rider
                        </a>
                        <a href="{{ route('admin.payroll.history') }}" class="nav-submenu-item {{ request()->routeIs('admin.payroll.history') ? 'active' : '' }}">
                            <span class="nav-submenu-icon"><svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span>
                            Riwayat Slip Gaji
                        </a>
                    </div>
                </div>

                <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                    <span class="nav-link-icon">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.38-2.92a2 2 0 0 1 2.22 0L13 7"/><path d="M2 7v14"/><path d="M22 7v14"/><path d="M22 21H2"/><path d="M13 7v14"/><path d="M7 21v-5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v5"/>
                        </svg>
                    </span> Cabang
                </a>

                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="nav-link-icon">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span> Pengaturan
                </a>

                <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <span class="nav-link-icon">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span> Kelola Akun
                </a>

            @elseif(auth()->user()->isBar())
                {{-- ====== BAR SIDEBAR (Limited Menu) ====== --}}
                @php
                    $isBarOperasionalActive = request()->routeIs(['admin.rider_sales.*', 'admin.productions.*', 'admin.spoiled_products.*', 'admin.available_stocks.*']);
                @endphp

                <div class="nav-section" x-data="{ open: true }">
                    <div class="nav-link dropdown-toggle" @click="open = !open" :class="{ 'active': {{ $isBarOperasionalActive ? 'true' : 'false' }} }">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                        </span>
                        <span>Operasional</span>
                        <svg class="dropdown-arrow" :class="{ 'rotated': open }" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    
                    <div x-show="open" x-collapse x-transition.opacity.duration.300ms class="nav-submenu">
                        <a href="{{ route('admin.productions.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.productions.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </span>
                            Riwayat Produksi
                        </a>
                        <a href="{{ route('admin.available_stocks.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.available_stocks.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </span>
                            Sisa Stok Tersedia
                        </a>
                        <a href="{{ route('admin.production_plan.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.production_plan.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </span>
                            Plan Produksi
                        </a>
                        <a href="{{ route('admin.spoiled_products.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.spoiled_products.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            </span>
                            Produk Basi
                        </a>
                        <a href="{{ route('admin.rider_sales.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_sales.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </span>
                            Input Penjualan
                        </a>
                    </div>
                </div>

            @elseif(auth()->user()->isAdmin())
                {{-- ====== ADMIN SIDEBAR ====== --}}
                @php
                    $isOperasionalActive = request()->routeIs(['dashboard', 'admin.rider_sales.*', 'admin.productions.*', 'admin.spoiled_products.*', 'admin.journals.*', 'admin.rider_finances.*', 'admin.office_kasbon.*', 'admin.rider_minus.*', 'admin.payroll.*', 'admin.rider_sales_report.*', 'admin.chats.*', 'admin.settings.*']);
                    $isManajemenActive = request()->routeIs(['carts.*', 'riders.*', 'bars.*', 'products.*', 'inventories.*', 'transactions.*', 'admin.artikel.*', 'admin.journal_categories.*']);
                @endphp

                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-link-icon">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                    </span> Dashboard
                </a>

                <div class="nav-section" x-data="{ open: {{ $isOperasionalActive && !request()->routeIs('dashboard') ? 'true' : 'false' }} }">
                    <div class="nav-link dropdown-toggle" @click="open = !open" :class="{ 'active': {{ $isOperasionalActive && !request()->routeIs('dashboard') ? 'true' : 'false' }} }">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                        </span>
                        <span>Operasional</span>
                        <svg class="dropdown-arrow" :class="{ 'rotated': open }" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    
                    <div x-show="open" x-collapse x-transition.opacity.duration.300ms class="nav-submenu">
                        <a href="{{ route('admin.productions.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.productions.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </span>
                            Riwayat Produksi
                        </a>
                        <a href="{{ route('admin.available_stocks.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.available_stocks.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            </span>
                            Sisa Stok Tersedia
                        </a>

                        <a href="{{ route('admin.spoiled_products.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.spoiled_products.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            </span>
                            Produk Basi
                        </a>
                        <a href="{{ route('admin.rider_sales.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_sales.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </span>
                            Input Penjualan
                        </a>
                        <a href="{{ route('admin.journals.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.journals.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M8 7h6"/><path d="M8 11h8"/></svg>
                            </span>
                            Jurnal Umum
                        </a>
                        <a href="{{ route('admin.rider_finances.kasbon') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_finances.kasbon') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </span>
                            Kasbon Rider
                        </a>
                        <a href="{{ route('admin.office_kasbon.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.office_kasbon.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            </span>
                            Kasbon Office
                        </a>
                        <a href="{{ route('admin.rider_finances.uang_makan') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_finances.uang_makan') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                            </span>
                            Uang Makan Rider
                        </a>
                        <a href="{{ route('admin.rider_minus.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_minus.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
                            </span>
                            Laporan Minus
                        </a>
                        <a href="{{ route('admin.payroll.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.payroll.index', 'admin.payroll.show') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><line x1="7" x2="7" y1="15" y2="15"/><line x1="12" x2="12" y1="15" y2="15"/></svg>
                            </span>
                            Slip Gaji Rider
                        </a>
                        <a href="{{ route('admin.payroll.history') }}" class="nav-submenu-item {{ request()->routeIs('admin.payroll.history') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </span>
                            Riwayat Slip Gaji
                        </a>
                        <a href="{{ route('admin.rider_sales_report.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.rider_sales_report.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                            </span>
                            Laporan Penjualan
                        </a>
                        <a href="{{ route('admin.chats.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.chats.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            </span>
                            Monitoring Chat
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>
                            Pengaturan
                        </a>
                    </div>
                </div>

                <div class="nav-section" x-data="{ open: {{ $isManajemenActive ? 'true' : 'false' }} }">
                    <div class="nav-link dropdown-toggle" @click="open = !open" :class="{ 'active': {{ $isManajemenActive ? 'true' : 'false' }} }">
                        <span class="nav-link-icon">
                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                        </span>
                        <span>Manajemen Data</span>
                        <svg class="dropdown-arrow" :class="{ 'rotated': open }" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>

                    <div x-show="open" x-collapse x-transition.opacity.duration.300ms class="nav-submenu">
                        <a href="{{ route('carts.index') }}" class="nav-submenu-item {{ request()->routeIs('carts.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                            </span>
                            Gerobak
                        </a>
                        <a href="{{ route('riders.index') }}" class="nav-submenu-item {{ request()->routeIs('riders.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            Rider
                        </a>
                        <a href="{{ route('bars.index') }}" class="nav-submenu-item {{ request()->routeIs('bars.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" x2="20" y1="8" y2="14"/><line x1="23" x2="17" y1="11" y2="11"/></svg>
                            </span>
                            Bar
                        </a>
                        <a href="{{ route('products.index') }}" class="nav-submenu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            </span>
                            Produk
                        </a>
                        <a href="{{ route('inventories.index') }}" class="nav-submenu-item {{ request()->routeIs('inventories.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                            </span>
                            Stok
                        </a>
                        <a href="{{ route('transactions.index') }}" class="nav-submenu-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </span>
                            Transaksi
                        </a>
                        <a href="{{ route('admin.artikel.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.artikel.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16L4 22Z"/><path d="M12 18h4"/><path d="M12 14h4"/><path d="M12 10h4"/><path d="M8 6h8"/></svg>
                            </span>
                            Artikel
                        </a>
                        <a href="{{ route('admin.journal_categories.index') }}" class="nav-submenu-item {{ request()->routeIs('admin.journal_categories.*') ? 'active' : '' }}">
                            <span class="nav-submenu-icon">
                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            Kategori Jurnal
                        </a>
                    </div>
                </div>
            @endif

            @if(auth()->user()->isRider())
                <div class="nav-section">
                    <div class="nav-section-title">Menu Rider</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-link-icon"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg></span> Dashboard
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

            @if(!auth()->user()->isBar())
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
            @endif
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
                @if(auth()->check() && !auth()->user()->isRider())
                <button class="desktop-menu-btn" @click="desktopSidebarCollapsed = !desktopSidebarCollapsed; localStorage.setItem('desktopSidebarCollapsed', desktopSidebarCollapsed)" title="Toggle Sidebar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                @endif
                <h1 class="topbar-title">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="topbar-actions">
                @if(auth()->check() && auth()->user()->isOwner())
                    <div x-data="{ open: false }" style="position: relative;">
                        <button @click="open = !open" @click.away="open = false" class="btn btn-secondary btn-sm flex-center" style="gap: 5px; height: 40px; padding: 0 15px; border-radius: 8px; font-weight: 600;">
                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.38-2.92a2 2 0 0 1 2.22 0L13 7"/><path d="M2 7v14"/><path d="M22 7v14"/><path d="M22 21H2"/><path d="M13 7v14"/><path d="M7 21v-5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v5"/></svg>
                            {{ \App\Models\Branch::find(activeBranchId())?->name ?? 'Pilih Cabang' }}
                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div x-show="open" style="position: absolute; right: 0; top: calc(100% + 8px); background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; min-width: 200px; z-index: 50; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); overflow: hidden; display: none;" :style="{ display: open ? 'block' : 'none' }">
                            @foreach(\App\Models\Branch::where('is_active', true)->get() as $b)
                                <form action="{{ route('branches.switch', $b->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="width: 100%; border: none; background: transparent; display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; color: var(--text-primary); text-decoration: none; border-bottom: 1px solid var(--border-color); font-size: 14px; cursor: pointer; text-align: left; {{ activeBranchId() == $b->id ? 'background: rgba(59, 130, 246, 0.1); font-weight: 600;' : '' }}">
                                        {{ $b->name }}
                                        @if(activeBranchId() == $b->id)
                                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if(auth()->check() && auth()->user()->isRider())
                    <button id="pwa-install-btn-rider" style="display:flex;" onclick="installRiderPWA()"
                        class="btn btn-secondary btn-sm flex-center" style="width: 40px; height: 40px; padding: 0; border-radius: 50%;"
                        title="Install App">
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
            @if(session('success') || session('error') || $errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });

                        @if(session('success'))
                            Toast.fire({
                                icon: 'success',
                                title: {!! json_encode(session('success')) !!}
                            });
                        @endif

                        @if(session('error'))
                            Toast.fire({
                                icon: 'error',
                                title: {!! json_encode(session('error')) !!}
                            });
                        @elseif($errors->any())
                            Toast.fire({
                                icon: 'error',
                                title: {!! json_encode($errors->first()) !!}
                            });
                        @endif
                    });
                </script>
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
                if (container.classList.contains('no-search')) return;

                // Membungkus input search dengan form untuk pencarian server-side
                const searchForm = document.createElement('form');
                searchForm.action = window.location.pathname; // Submit ke halaman saat ini
                searchForm.method = 'GET';
                searchForm.style.display = 'flex';
                searchForm.style.justifyContent = 'flex-end';
                searchForm.style.marginBottom = '1rem';
                searchForm.classList.add('print-hide');
                
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

                // Membuat wrapper untuk input + button search
                const searchWrapper = document.createElement('div');
                searchWrapper.style.cssText = 'position: relative; display: flex; align-items: center; max-width: 320px; width: 100%;';

                // Membuat element input
                const searchInput = document.createElement('input');
                searchInput.setAttribute('type', 'text');
                searchInput.setAttribute('name', 'search');
                
                // Isi input dengan parameter pencarian saat ini jika ada
                const currentSearch = new URLSearchParams(window.location.search).get('search');
                if (currentSearch) {
                    searchInput.value = currentSearch;
                }

                searchInput.setAttribute('placeholder', 'Ketik & tekan Enter...');
                searchInput.className = 'form-input search-input-global'; 
                searchInput.style.cssText = 'width: 100%; padding-right: 3rem; border-radius: 12px; margin: 0;';

                // Membuat tombol search icon yang bisa diklik
                const searchBtn = document.createElement('button');
                searchBtn.type = 'submit';
                searchBtn.className = 'search-btn-icon';
                searchBtn.setAttribute('title', 'Cari');
                searchBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';

                searchWrapper.appendChild(searchInput);
                searchWrapper.appendChild(searchBtn);
                searchForm.appendChild(searchWrapper);
                
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

            // Initialize Select2 globally for all select inputs
            $('select.form-input').each(function() {
                let $this = $(this);
                // Ensure dropdown works inside modals
                let modal = $this.closest('.modal-overlay-animate, .account-modal-overlay, [id$="Modal"]');
                let config = {
                    width: '100%',
                    placeholder: $this.find('option[value=""]').text() || 'Pilih opsi...',
                };
                if (modal.length) {
                    config.dropdownParent = modal;
                }
                $this.select2(config).on('select2:select', function (e) {
                    // Trigger native event for Vanilla JS / Alpine JS compatibility
                    this.dispatchEvent(new Event('change', { bubbles: true }));
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
            if (topbarBtn) topbarBtn.style.display = 'flex';

            const banner = document.getElementById('pwa-rider-install');
            if (banner) banner.remove();
        }

        window.addEventListener('appinstalled', (evt) => {
            const topbarBtn = document.getElementById('pwa-install-btn-rider');
            if (topbarBtn) topbarBtn.style.display = 'flex';
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

    @if(auth()->check())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Echo) {
                    window.Echo.private('App.Models.User.{{ auth()->user()->id }}')
                        .listen('MessageSent', (e) => {
                            // Cek jika sedang berada di halaman chat tersebut
                            const isChatPage = window.location.pathname.includes(`/rider/chat/${e.conversation_id}`) || 
                                               window.location.pathname.includes(`/customer/chat/${e.conversation_id}`);
                            
                            if (!isChatPage) {
                                // Tampilkan notifikasi browser jika diizinkan
                                if ("Notification" in window && Notification.permission === "granted") {
                                    new Notification("Pesan Baru dari " + e.sender_name, {
                                        body: e.body ? e.body : (e.attachment_path ? "Mengirim lampiran" : "Pesan baru"),
                                        icon: "/icons/rider-192x192.png"
                                    });
                                } else if ("Notification" in window && Notification.permission !== "denied") {
                                    Notification.requestPermission().then(function (permission) {
                                        if (permission === "granted") {
                                            new Notification("Pesan Baru dari " + e.sender_name, {
                                                body: e.body ? e.body : (e.attachment_path ? "Mengirim lampiran" : "Pesan baru"),
                                                icon: "/icons/rider-192x192.png"
                                            });
                                        }
                                    });
                                }
                                
                                // Opsi: Refresh badge counter unread chat (jika diperlukan)
                                // Karena ini layout global, mungkin kita bisa inject/fetch API untuk update counter
                            }
                        });
                }
            });
        </script>
    @endif

    @stack('scripts')
    <script src="{{ asset('js/excel-nav.js') }}"></script>
</body>
</html>

