<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ===== SEO META TAGS ===== --}}
    <title>Tether Brew – Kopi Keliling Segar & Murah di Medan | Mulai Rp 8.000</title>
    <meta name="description" content="Tether Brew adalah kopi keliling terbaik di Medan. Kopi segar, murah mulai Rp 8.000, langsung diantar ke lokasimu. Temukan gerobak terdekat via peta realtime. Cold Brew, Americano, Matcha & 13+ varian lainnya!">
    <meta name="keywords" content="kopi Medan, kopi keliling Medan, kopi murah Medan, kopi segar Medan, gerobak kopi Medan, kopi terdekat Medan, Tether Brew, cold brew Medan, es kopi susu Medan, kopi online Medan, jual kopi Medan, kopi delivery Medan, kopi Sumatera Utara">
    <meta name="author" content="Tether Brew">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- ===== GEO META (Medan, Sumatera Utara) ===== --}}
    <meta name="geo.region" content="ID-SU">
    <meta name="geo.placename" content="Medan">
    <meta name="geo.position" content="3.5952;98.6722">
    <meta name="ICBM" content="3.5952, 98.6722">

    {{-- ===== OPEN GRAPH (Facebook, WhatsApp, etc.) ===== --}}
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="Tether Brew">
    <meta property="og:title" content="Tether Brew – Kopi Keliling Segar & Murah di Medan">
    <meta property="og:description" content="Kopi segar keliling di Medan mulai Rp 8.000! Temukan gerobak Tether Brew terdekat, lihat menu & stok realtime, pesan langsung via WhatsApp.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('favicon.webp') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:alt" content="Tether Brew - Kopi Keliling Medan">

    {{-- ===== TWITTER CARD ===== --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Tether Brew – Kopi Keliling Segar & Murah di Medan">
    <meta name="twitter:description" content="Kopi segar keliling di Medan mulai Rp 8.000! 13+ varian menu. Temukan gerobak terdekat via peta realtime.">
    <meta name="twitter:image" content="{{ asset('favicon.webp') }}">

    {{-- ===== JSON-LD STRUCTURED DATA ===== --}}
    <script type="application/ld+json">
    {!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FoodEstablishment',
    'name' => 'Tether Brew',
    'alternateName' => 'Tether Brew Kopi Keliling Medan',
    'description' => 'Tether Brew adalah layanan kopi keliling di Medan yang menyajikan kopi segar berkualitas dengan harga terjangkau mulai dari Rp 8.000. Tersedia 13+ varian menu termasuk Cold Brew, Honey Brew, Americano, Matcha, dan lainnya.',
    'url' => url('/'),
    'logo' => asset('favicon.webp'),
    'image' => asset('favicon.webp'),
    'telephone' => '',
    'servesCuisine' => ['Kopi', 'Minuman', 'Coffee'],
    'priceRange' => 'Rp 8.000 - Rp 15.000',
    'currenciesAccepted' => 'IDR',
    'paymentAccepted' => 'Cash, Transfer',
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => 'Medan',
        'addressRegion' => 'Sumatera Utara',
        'addressCountry' => 'ID',
    ],
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude' => 3.5952,
        'longitude' => 98.6722,
    ],
    'areaServed' => [
        '@type' => 'City',
        'name' => 'Medan',
    ],
    'hasMenu' => [
        '@type' => 'Menu',
        'hasMenuSection' => [
            [
                '@type' => 'MenuSection',
                'name' => 'Kopi',
                'hasMenuItem' => [
                    ['@type' => 'MenuItem', 'name' => 'Cold Brew', 'offers' => ['@type' => 'Offer', 'price' => '15000', 'priceCurrency' => 'IDR']],
                    ['@type' => 'MenuItem', 'name' => 'Honey Brew', 'offers' => ['@type' => 'Offer', 'price' => '15000', 'priceCurrency' => 'IDR']],
                    ['@type' => 'MenuItem', 'name' => 'Americano', 'offers' => ['@type' => 'Offer', 'price' => '8000', 'priceCurrency' => 'IDR']],
                    ['@type' => 'MenuItem', 'name' => 'Caramel Brew', 'offers' => ['@type' => 'Offer', 'price' => '12000', 'priceCurrency' => 'IDR']],
                    ['@type' => 'MenuItem', 'name' => 'Vanilla Brew', 'offers' => ['@type' => 'Offer', 'price' => '12000', 'priceCurrency' => 'IDR']],
                ],
            ],
            [
                '@type' => 'MenuSection',
                'name' => 'Non-Kopi',
                'hasMenuItem' => [
                    ['@type' => 'MenuItem', 'name' => 'Matcha Brew', 'offers' => ['@type' => 'Offer', 'price' => '12000', 'priceCurrency' => 'IDR']],
                    ['@type' => 'MenuItem', 'name' => 'Cokelat Brew', 'offers' => ['@type' => 'Offer', 'price' => '12000', 'priceCurrency' => 'IDR']],
                ],
            ],
        ],
    ],
    'sameAs' => ['https://www.instagram.com/tetherbrew/'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- ===== FAVICON ===== --}}
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/landing.css'])
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
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
                localStorage.setItem('theme', document.documentElement.classList.contains('light-theme') ? 'light' : 'dark');
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
</head>
<body x-data="{ mobileOpen: false, darkMode: localStorage.getItem('theme') !== 'light' }">

    {{-- ===== NAVBAR ===== --}}
    <nav class="navbar" id="navbar">
        <div class="navbar-menu" :class="{ 'mobile-open': mobileOpen }">
            <a href="#home" @click="mobileOpen = false">Beranda</a>
            <a href="#about" @click="mobileOpen = false">Tentang</a>
            <a href="#menu" @click="mobileOpen = false">Menu</a>
            <a href="#maps" @click="mobileOpen = false">Lokasi</a>
            <a href="#contact" @click="mobileOpen = false">Kontak</a>
            <button @click="window.switchTheme($event, () => darkMode = !darkMode)" class="theme-toggle-btn-landing">
                <template x-if="darkMode">
                    <svg width="1.4em" height="1.4em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"/><line x1="12" x2="12" y1="1" y2="3"/><line x1="12" x2="12" y1="21" y2="23"/><line x1="4.22" x2="5.64" y1="4.22" y2="5.64"/><line x1="18.36" x2="19.78" y1="18.36" y2="19.78"/><line x1="1" x2="3" y1="12" y2="12"/><line x1="21" x2="23" y1="12" y2="12"/><line x1="4.22" x2="5.64" y1="19.78" y2="18.36"/><line x1="18.36" x2="19.78" y1="5.64" y2="4.22"/>
                    </svg>
                </template>
                <template x-if="!darkMode">
                    <svg width="1.4em" height="1.4em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </template>
            </button>
        </div>
        <button class="mobile-toggle" @click="mobileOpen = !mobileOpen" >☰</button>
    </nav>

    {{-- ===== HOME / HERO ===== --}}
    <section class="hero" id="home">
        <div class="hero-bg">
            <img src="/storage/image/kopi-tether-new.webp" alt="Tether Brew Coffee Background">
        </div>
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                Tether Brew · Kopi Keliling Medan
            </div>
            <h1>Kopi Segar & Murah di<br><span class="highlight">Medan</span> dari Tether Brew</h1>
            <p>Temukan gerobak kopi keliling Tether Brew terdekat di Medan. Kopi segar berkualitas mulai Rp 8.000, cek lokasi, menu, dan stok secara realtime.</p>
            <div class="hero-cta">
                <a href="#maps" class="btn-hero btn-hero-primary">
                    <svg class="icon-two-tone" width="1.4em" height="1.4em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    Lihat Peta
                </a>
                <a href="#menu" class="btn-hero btn-hero-secondary">
                    <svg class="icon-two-tone" width="1.4em" height="1.4em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" x2="6" y1="1" y2="4"/><line x1="10" x2="10" y1="1" y2="4"/><line x1="14" x2="14" y1="1" y2="4"/>
                    </svg>
                    Lihat Menu
                </a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-value" id="stat-carts">-</div>
                    <div class="hero-stat-label">Gerobak Aktif</div>
                </div>
                <div>
                    <div class="hero-stat-value" id="stat-menus">-</div>
                    <div class="hero-stat-label">Varian Menu</div>
                </div>
                <div>
                    <div class="hero-stat-value">8K</div>
                    <div class="hero-stat-label">Mulai Dari</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== MAPS ===== --}}
    <section class="map-section section-relative" id="maps">
        <div class="deco-bg-wrapper">
            <img src="/storage/image/kopi-tether-new.webp" class="deco-cup deco-cup-1" alt="">
        </div>
        <div class="section-header">
            <div class="section-tag">LOKASI</div>
            <h2 class="section-title">Temukan Gerobak Terdekat</h2>
            <p class="section-subtitle">Klik marker hijau di peta untuk melihat detail menu dan stok yang tersedia</p>
        </div>
        <div class="map-wrapper">
            <div class="map-overlay">
                <div class="dot"></div>
                <span id="cart-count-label">Memuat data...</span>
            </div>
            <div id="map"></div>
        </div>
    </section>

    {{-- ===== MENU ===== --}}
    <section class="menu-section section-relative" id="menu" x-data="{ activeTab: 'semua' }">
        <div class="deco-bg-wrapper">
            <img src="/storage/image/kopi-tether-new.webp" class="deco-cup deco-cup-2" alt="">
        </div>
        <div class="section-header">
            <div class="section-tag"> MENU</div>
            <h2 class="section-title">Menu Tether Brew</h2>
            <p class="section-subtitle">Nikmati berbagai varian kopi dan minuman segar pilihan kami</p>
        </div>

        <div class="menu-tabs">
            <button class="menu-tab" :class="{ active: activeTab === 'semua' }" @click="activeTab = 'semua'">Semua</button>
            <button class="menu-tab" :class="{ active: activeTab === 'kopi' }" @click="activeTab = 'kopi'">☕ Coffee</button>
            <button class="menu-tab" :class="{ active: activeTab === 'non-kopi' }" @click="activeTab = 'non-kopi'"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em; margin-right:4px;"><path d="M6 9l1.5 11.5A2 2 0 0 0 9.5 22h5a2 2 0 0 0 2-1.5L18 9" /><line x1="4" y1="9" x2="20" y2="9" /><path d="M5 9 A 7 5 0 0 1 19 9" /><line x1="12" y1="4" x2="14" y2="0" /></svg> Non-Coffee</button>
        </div>

        <div class="menu-grid" id="menu-grid">
            {{-- Filled by JS --}}
        </div>
    </section>

    {{-- ===== GEROBAK LIST ===== --}}
    <section class="gerobak-section" id="gerobak">
        <div class="section-header">
            <div class="section-tag">GEROBAK</div>
            <h2 class="section-title">Gerobak Aktif</h2>
            <p class="section-subtitle">Klik kartu untuk zoom ke lokasi gerobak di peta</p>
        </div>
        <div class="gerobak-grid" id="cart-grid">
            <div class="empty-state-text">
                Memuat data gerobak...
            </div>
        </div>
    </section>

    {{-- ===== ABOUT ===== --}}
    <section class="about-section section-relative" id="about">
        <div class="deco-bg-wrapper">
            <img src="/storage/image/kopi-tether-new.webp" class="deco-cup deco-cup-3" alt="">
        </div>
        <div class="section-header">
            <div class="section-tag">TENTANG</div>
            <h2 class="section-title">Kenapa Tether Brew?</h2>
            <p class="section-subtitle">Kopi keliling terbaik di Medan — segar, murah, dan selalu dekat dengan lokasimu</p>
        </div>
        <div class="about-grid">
            <div class="about-card">
                <div class="about-icon">
                    <svg class="icon-two-tone" width="1.5em" height="1.5em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" x2="6" y1="1" y2="4"/><line x1="10" x2="10" y1="1" y2="4"/><line x1="14" x2="14" y1="1" y2="4"/>
                    </svg>
                </div>
                <h3>Kopi Segar Berkualitas</h3>
                <p>Biji kopi pilihan dari petani lokal Sumatera Utara, diolah fresh setiap hari untuk menghasilkan kopi terbaik di Medan.</p>
            </div>
            <div class="about-card">
                <div class="about-icon">
                    <svg class="icon-two-tone" width="1.5em" height="1.5em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 12V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V15"/><rect x="16" y="12" width="6" height="3" rx="1"/>
                    </svg>
                </div>
                <h3>Harga Murah Meriah</h3>
                <p>Mulai dari Rp 8.000 saja! Kopi premium khas Medan yang bisa dinikmati semua kalangan tanpa menguras dompet.</p>
            </div>
            <div class="about-card">
                <div class="about-icon">
                    <svg class="icon-two-tone" width="1.5em" height="1.5em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <h3>Keliling & Selalu Dekat</h3>
                <p>Gerobak kopi kami keliling di berbagai lokasi strategis di Medan. Cek peta realtime untuk menemukan yang terdekat!</p>
            </div>
        </div>
    </section>

    {{-- ===== CONTACT ===== --}}
    <section class="contact-section" id="contact">
        <div class="contact-inner">
            <h3>Hubungi Kami</h3>
            <p>Mau jadi mitra Tether Brew? Atau ada pertanyaan? Jangan ragu untuk menghubungi kami.</p>
            <div class="contact-links">
                <a href="#" class="contact-link"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" style="display:inline-block; vertical-align:-0.2em; margin-right:4px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg> WhatsApp</a>
                <a href="#" class="contact-link"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.2em; margin-right:4px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> Email</a>
                <a href="https://www.instagram.com/tetherbrew/" class="contact-link"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.2em; margin-right:4px;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg> Instagram</a>
            </div>
        </div>
    </section>

    {{-- ===== BOTTOM NAV MOBILE ===== --}}
    <nav class="bottom-nav">
        <div class="bottom-nav-inner">
            <a href="#home" class="bottom-nav-item active" data-section="home">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Beranda</span>
            </a>
            <a href="#maps" class="bottom-nav-item" data-section="maps">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Lokasi</span>
            </a>
            <a href="#menu" class="bottom-nav-item" data-section="menu">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line>
                </svg>
                <span>Menu</span>
            </a>
            <a href="#about" class="bottom-nav-item" data-section="about">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Tentang</span>
            </a>
            <a href="#contact" class="bottom-nav-item" data-section="contact">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span>Kontak</span>
            </a>
        </div>
    </nav>

    {{-- ===== ORDER PANEL (Slide-up) ===== --}}
    <div id="order-panel" class="order-panel">
        <div class="order-panel-backdrop" id="order-backdrop"></div>
        <div class="order-panel-sheet" id="order-sheet">
            <div class="order-panel-handle"><div class="handle-bar"></div></div>
            <div class="order-panel-header">
                <div>
                    <h3 id="order-cart-name">Nama Gerobak</h3>
                    <p id="order-rider-name" class="order-rider">Rider: -</p>
                    <div id="order-distance" class="order-distance"></div>
                </div>
                <button id="order-close-btn" class="order-close-btn">✕</button>
            </div>
            <div class="order-panel-body" id="order-menu-list">
                {{-- Menu items rendered by JS --}}
            </div>
            <div class="order-notes-wrapper">
                <label for="order-notes">Catatan Pesanan</label>
                <textarea id="order-notes" class="order-notes" placeholder="Contoh: Gula sedikit, Es banyak, Pisah cup..." rows="2"></textarea>
            </div>
            <div class="order-eta-wrapper">
                <label for="order-eta">Estimasi Saya Sampai</label>
                <select id="order-eta" class="order-eta-select">
                    <option value="">— Pilih estimasi —</option>
                    <option value="Saya sudah di lokasi">Saya sudah di lokasi</option>
                    <option value="± 5 menit"> ± 5 menit</option>
                    <option value="± 10 menit"> ± 10 menit</option>
                    <option value="± 15 menit"> ± 15 menit</option>
                    <option value="± 30 menit"> ± 30 menit</option>
                    <option value="Lebih dari 30 menit"> Lebih dari 30 menit</option>
                    </select>
                    <div id="order-eta-hint" class="order-eta-hint"></div>
                    </div>
                    <div class="order-panel-footer">
                        <div class="order-summary">
                            <div class="order-total-row">
                                <span>Total</span>
                                <span id="order-total-price" class="order-total-price">Rp 0</span>
                            </div>
                            <div id="order-item-count" class="order-item-count">0 item dipilih</div>
                        </div>
                        <div class="order-action-buttons">
                            <a id="order-nav-btn" href="#" target="_blank" class="order-nav-btn">
                                <svg class="icon-two-tone" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"
                                    fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                                </svg>
                                Arahkan
                            </a>
                            <a id="order-loc-btn" href="#" target="_blank" class="order-loc-btn disabled">
                                <svg class="icon-two-tone" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"
                                    fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Kirim Lokasi
                            </a>
                        </div>
                        <div class="order-action-buttons" style="margin-top: 8px;">
                            <a id="order-wa-btn" href="#" target="_blank" class="order-wa-btn disabled">
                                <svg class="icon-two-tone" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"
                                    fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                </svg>
                                Pesan via WhatsApp
                            </a>
                        </div>
                    </div>
                    </div>
                    </div>
                    
                    {{-- ===== FOOTER ===== --}}
                    <footer class="footer">
                        <p>© {{ date('Y') }} Tether Brew.</p>
                    </footer>
                    
                    <script>
                        // Navbar scroll effect
                        window.addEventListener('scroll', () => {
                            document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 20);
                        });

                        // Initialize map
                        const map = L.map('map', { attributionControl: false }).setView([-6.2088, 106.8456], 13);

                        const darkTiles = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
                        const lightTiles = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';

                        let activeTileLayer = L.tileLayer(localStorage.getItem('theme') === 'light' ? lightTiles : darkTiles, {
                            maxZoom: 19
                        }).addTo(map);

                        window.addEventListener('theme-changed', (e) => {
                            map.removeLayer(activeTileLayer);
                            activeTileLayer = L.tileLayer(e.detail.theme === 'light' ? lightTiles : darkTiles, {
                                maxZoom: 19
                            }).addTo(map);
                        });

                        function formatRupiah(num) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                        }

                        // =============================================
                        // USER LOCATION (for distance estimation)
                        // =============================================
                        let userLatLng = null;
                        if ('geolocation' in navigator) {
                            navigator.geolocation.getCurrentPosition(
                                pos => { userLatLng = L.latLng(pos.coords.latitude, pos.coords.longitude); },
                                () => { console.log('Geolocation denied or unavailable'); },
                                { enableHighAccuracy: false, timeout: 8000 }
                            );
                        }

                        function getDistanceText(cartLat, cartLng) {
                            if (!userLatLng) return null;
                            const cartPos = L.latLng(cartLat, cartLng);
                            const meters = userLatLng.distanceTo(cartPos);
                            if (meters < 1000) return Math.round(meters) + ' m';
                            return (meters / 1000).toFixed(1) + ' km';
                        }

                        function getDistanceMeters(cartLat, cartLng) {
                            if (!userLatLng) return null;
                            const cartPos = L.latLng(cartLat, cartLng);
                            return userLatLng.distanceTo(cartPos);
                        }

                        function estimateETA(meters) {
                            if (meters === null) return '';
                            if (meters < 500) return 'Saya sudah di lokasi';
                            if (meters < 1000) return '± 5 menit';
                            if (meters < 2000) return '± 10 menit';
                            if (meters < 5000) return '± 15 menit';
                            if (meters < 10000) return '± 30 menit';
                            return 'Lebih dari 30 menit';
                        }

                        // =============================================
                        // ORDER PANEL LOGIC
                        // =============================================
                        let currentOrderCart = null;
                        let orderItems = []; // { name, price, stock, qty }

                        const orderPanel = document.getElementById('order-panel');
                        const orderBackdrop = document.getElementById('order-backdrop');
                        const orderSheet = document.getElementById('order-sheet');
                        const orderCartName = document.getElementById('order-cart-name');
                        const orderRiderName = document.getElementById('order-rider-name');
                        const orderDistance = document.getElementById('order-distance');
                        const orderMenuList = document.getElementById('order-menu-list');
                        const orderNotes = document.getElementById('order-notes');
                        const orderTotalPrice = document.getElementById('order-total-price');
                        const orderItemCount = document.getElementById('order-item-count');
                        const orderWaBtn = document.getElementById('order-wa-btn');
                        const orderNavBtn = document.getElementById('order-nav-btn');
                        const orderLocBtn = document.getElementById('order-loc-btn');
                        const orderEta = document.getElementById('order-eta');
                        const orderEtaHint = document.getElementById('order-eta-hint');
                        const orderCloseBtn = document.getElementById('order-close-btn');

                        function openOrderPanel(cart) {
                            currentOrderCart = cart;
                            orderItems = cart.menu.map(m => ({ ...m, qty: 0 }));

                            orderCartName.textContent = cart.name;
                            orderRiderName.textContent = 'Rider: ' + cart.rider;
                            orderNotes.value = '';

                            // Distance estimation
                            const dist = getDistanceText(cart.latitude, cart.longitude);
                            if (dist) {
                                orderDistance.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-2px;margin-right:4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> ± ${dist} dari lokasi Anda`;
                                orderDistance.style.display = 'flex';
                            } else {
                                orderDistance.style.display = 'none';
                            }

                            // Navigation button
                            orderNavBtn.href = `https://www.google.com/maps/dir/?api=1&destination=${cart.latitude},${cart.longitude}`;

                            // ETA auto-fill based on distance
                            const meters = getDistanceMeters(cart.latitude, cart.longitude);
                            const autoEta = estimateETA(meters);
                            if (autoEta) {
                                orderEta.value = autoEta;
                                orderEtaHint.textContent = `Otomatis berdasarkan jarak ± ${dist}`;
                                orderEtaHint.style.display = 'block';
                            } else {
                                orderEta.value = '';
                                orderEtaHint.textContent = 'Aktifkan lokasi untuk estimasi otomatis';
                                orderEtaHint.style.display = 'block';
                            }

                            // Location share button
                            if (userLatLng && currentOrderCart?.whatsapp) {
                                orderLocBtn.classList.remove('disabled');
                                orderLocBtn.href = generateLocationShareLink();
                            } else {
                                orderLocBtn.classList.add('disabled');
                                orderLocBtn.href = '#';
                            }

                            renderOrderMenu();
                            updateOrderTotal();

                            orderPanel.classList.add('open');
                            document.body.style.overflow = 'hidden';
                        }

                        function closeOrderPanel() {
                            orderPanel.classList.remove('open');
                            document.body.style.overflow = '';
                            currentOrderCart = null;
                        }

                        orderCloseBtn.addEventListener('click', closeOrderPanel);
                        orderBackdrop.addEventListener('click', closeOrderPanel);

                        function renderOrderMenu() {
                            if (orderItems.length === 0) {
                                orderMenuList.innerHTML = '<div class="order-empty">Belum ada menu tersedia di gerobak ini.</div>';
                                return;
                            }
                            orderMenuList.innerHTML = orderItems.map((item, i) => `
                                    <div class="order-menu-row">
                                        <div class="order-menu-info">
                                            <span class="order-menu-name">${item.name}</span>
                                            <span class="order-menu-price">${formatRupiah(item.price)}</span>
                                            <span class="order-menu-stock">Stok: ${item.stock}</span>
                                        </div>
                                        <div class="order-qty-controls">
                                            <button class="order-qty-btn" onclick="changeQty(${i}, -1)" ${item.qty <= 0 ? 'disabled' : ''}>−</button>
                                            <span class="order-qty-value">${item.qty}</span>
                                            <button class="order-qty-btn" onclick="changeQty(${i}, 1)" ${item.qty >= item.stock ? 'disabled' : ''}>+</button>
                                        </div>
                                    </div>
                                `).join('');
                        }

                        function changeQty(index, delta) {
                            const item = orderItems[index];
                            const newQty = item.qty + delta;
                            if (newQty < 0 || newQty > item.stock) return;
                            item.qty = newQty;
                            renderOrderMenu();
                            updateOrderTotal();
                        }

                        function updateOrderTotal() {
                            const selected = orderItems.filter(i => i.qty > 0);
                            const total = selected.reduce((sum, i) => sum + (i.price * i.qty), 0);
                            const count = selected.reduce((sum, i) => sum + i.qty, 0);

                            orderTotalPrice.textContent = formatRupiah(total);
                            orderItemCount.textContent = count + ' item dipilih';

                            if (count > 0 && currentOrderCart?.whatsapp) {
                                orderWaBtn.classList.remove('disabled');
                                orderWaBtn.href = generateWhatsAppLink();
                            } else {
                                orderWaBtn.classList.add('disabled');
                                orderWaBtn.href = '#';
                            }
                        }

                        function generateWhatsAppLink() {
                            if (!currentOrderCart) return '#';
                            const phone = currentOrderCart.whatsapp;
                            const selected = orderItems.filter(i => i.qty > 0);
                            const total = selected.reduce((sum, i) => sum + (i.price * i.qty), 0);
                            const notes = orderNotes.value.trim();
                            const eta = orderEta.value;

                            let msg = `Halo Tether Brew *${currentOrderCart.name}*,\nSaya mau pesan:\n\n`;
                            selected.forEach(item => {
                                const sub = item.price * item.qty;
                                msg += ` ${item.name} x${item.qty} = ${formatRupiah(sub)}\n`;
                            });
                            msg += `\n *Total: ${formatRupiah(total)}*`;
                            if (eta) msg += `\n *Estimasi saya sampai: ${eta}*`;
                            if (notes) msg += `\n\n Catatan: ${notes}`;
                            if (userLatLng) {
                                msg += `\n\n Lokasi saya: https://maps.google.com/?q=${userLatLng.lat},${userLatLng.lng}`;
                            }
                            msg += `\n\nMohon konfirmasi ketersediaannya dan jangan kemana-mana dulu ya! `;

            return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
        }

        function generateLocationShareLink() {
            if (!currentOrderCart || !userLatLng) return '#';
            const phone = currentOrderCart.whatsapp;
            const dist = getDistanceText(currentOrderCart.latitude, currentOrderCart.longitude);
            const mapsLink = `https://maps.google.com/?q=${userLatLng.lat},${userLatLng.lng}`;

            let msg = `Halo Tether Brew *${currentOrderCart.name}*! 👋\n\n`;
            msg += `Saya tertarik untuk pesan kopi. Ini lokasi saya:\n`;
            msg += `📍 ${mapsLink}\n\n`;
            if (dist) msg += `Jarak saya ke gerobak Anda: ± ${dist}\n\n`;
            msg += `Apakah memungkinkan untuk mendekat ke lokasi saya? Terima kasih! 🙏`;

            return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
        }

        // Update WA link on notes/eta change
        orderNotes.addEventListener('input', updateOrderTotal);
        orderEta.addEventListener('change', function() {
            orderEtaHint.textContent = '';
            orderEtaHint.style.display = 'none';
            updateOrderTotal();
        });

        // =============================================
        // MENU & MAP DATA
        // =============================================
        let allMenuItems = [];
        let allCartsData = [];

        fetch('/api/carts-map')
            .then(r => r.json())
            .then(carts => {
                allCartsData = carts;

                // Stats
                document.getElementById('stat-carts').textContent = carts.length;
                let menuSet = new Map();
                carts.forEach(c => c.menu.forEach(m => {
                    if (!menuSet.has(m.name)) menuSet.set(m.name, m);
                }));
                document.getElementById('stat-menus').textContent = menuSet.size;
                document.getElementById('cart-count-label').textContent = carts.length + ' gerobak aktif';

                allMenuItems = Array.from(menuSet.values());
                renderMenu('semua');

                document.querySelectorAll('.menu-tab').forEach(tab => {
                    tab.addEventListener('click', () => {
                        const cat = tab.textContent.includes('Coffee') && !tab.textContent.includes('Non')
                            ? 'kopi' : tab.textContent.includes('Non') ? 'non-kopi' : 'semua';
                        renderMenu(cat);
                    });
                });

                const markers = {};
                const brewIcon = L.divIcon({
                    className: 'custom-marker',
                    html: '<div class="custom-marker-icon" style="padding:0; overflow:hidden;"><img src="{{ asset("favicon.webp") }}?v={{ time() }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;" alt="Marker" /></div>',
                    iconSize: [40, 40], iconAnchor: [20, 20], popupAnchor: [0, -24]
                });

                function renderCartsOnMap(carts, initial = false) {
                    const bounds = [];
                    carts.forEach(cart => {
                        const latlng = [cart.latitude, cart.longitude];
                        bounds.push(latlng);

                        // Build popup (info only, order via panel)
                        const distText = getDistanceText(cart.latitude, cart.longitude);
                        const popupContent = `
                            <div class="popup-title">${cart.name}</div>
                            <div class="popup-rider">${cart.rider}${distText ? ' · 📍 ' + distText : ''}</div>
                            <div class="popup-menu-title">${cart.menu.length} menu tersedia</div>
                            <div class="popup-updated">Update: ${cart.updated_at}</div>
                            <div class="popup-contact-wrapper">
                                <button class="popup-order-btn" onclick="openOrderPanel(window.__cartsData.find(c=>c.id===${cart.id}))"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.2em;margin-right:4px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg> Pesan dari Gerobak Ini</button>
                            </div>
                        `;

                        if (markers[cart.id]) {
                            markers[cart.id].setLatLng(latlng).setPopupContent(popupContent);
                        } else {
                            markers[cart.id] = L.marker(latlng, { icon: brewIcon })
                                .addTo(map)
                                .bindPopup(popupContent, { maxWidth: 280 });
                        }
                    });

                    // Store globally for popup button access
                    window.__cartsData = carts;

                    if (initial && bounds.length > 0) map.fitBounds(bounds, { padding: [50, 50] });
                }

                function renderCartCards(carts) {
                    const grid = document.getElementById('cart-grid');
                    document.getElementById('cart-count-label').textContent = carts.length + ' gerobak aktif · live';

                    if (carts.length === 0) {
                        grid.innerHTML = '<div class="gerobak-empty-state">Belum ada gerobak aktif saat ini.</div>';
                        return;
                    }

                    grid.innerHTML = carts.map(cart => {
                        const distText = getDistanceText(cart.latitude, cart.longitude);
                        return `
                        <div class="gerobak-card" onclick="map.setView([${cart.latitude},${cart.longitude}],16); document.getElementById('maps').scrollIntoView({behavior:'smooth'});">
                            <div class="gerobak-card-top">
                                <div class="gerobak-card-name"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em; margin-right:4px;"><path d="M6 9l1.5 11.5A2 2 0 0 0 9.5 22h5a2 2 0 0 0 2-1.5L18 9" /><line x1="4" y1="9" x2="20" y2="9" /><path d="M5 9 A 7 5 0 0 1 19 9" /><line x1="12" y1="4" x2="14" y2="0" /></svg> ${cart.name}</div>
                                <div class="gerobak-badge">Aktif</div>
                            </div>
                            <div class="gerobak-card-rider"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/></svg>${cart.rider} · <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:2px; margin-left:4px; margin-top:-2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>${cart.updated_at}${distText ? ' · 📍 ' + distText : ''}</div>
                            <div class="gerobak-card-tags">
                                ${cart.menu.slice(0, 4).map(m => `<span class="gerobak-card-tag">${m.name}</span>`).join('')}
                                ${cart.menu.length > 4 ? `<span class="gerobak-card-tag">+${cart.menu.length - 4}</span>` : ''}
                            </div>
                            <div class="gerobak-card-actions" onclick="event.stopPropagation()">
                                <button class="btn-cart-wa" onclick="openOrderPanel(window.__cartsData.find(c=>c.id===${cart.id}))"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.2em; margin-right:4px;"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> Pesan</button>
                            </div>
                        </div>
                    `}).join('');
                }

                // Initial load
                renderCartsOnMap(carts, true);
                renderCartCards(carts);

                // Periodic refresh (every 8 seconds)
                setInterval(() => {
                    fetch('/api/carts-map')
                        .then(r => r.json())
                        .then(carts => {
                            allCartsData = carts;
                            renderCartsOnMap(carts);
                            renderCartCards(carts);
                        })
                        .catch(err => console.warn('Refresh failed:', err));
                }, 8000);
            })
            .catch(err => {
                console.error('Initial load failed:', err);
                document.getElementById('cart-count-label').textContent = 'Gagal memuat data';
            });

        function renderMenu(category) {
            const grid = document.getElementById('menu-grid');
            const items = category === 'semua' ? allMenuItems : allMenuItems.filter(m => m.category === category);

            if (items.length === 0) {
                grid.innerHTML = '<div class="empty-state-text">Tidak ada menu</div>';
                return;
            }

            grid.innerHTML = items.map(m => `
                <div class="menu-card">
                    <div class="menu-card-top">
                        <div class="menu-card-bg-circle"></div>
                        <div class="menu-card-hero">
                            ${m.image ? '<img src="/storage/image/' + m.image + '" alt="' + m.name + '">' : '<img src="/storage/image/kopi-tether-new.webp" alt="' + m.name + '">'}
                        </div>
                        <svg class="menu-card-wave" viewBox="0 0 1000 100" preserveAspectRatio="none"><path  d="M0,50 C300,120 700,-20 1000,50 L1000,105 L0,105 Z"></path></svg>
                    </div>
                    <div class="menu-card-info">
                        <div class="menu-card-name">${m.name}</div>
                        <div class="menu-card-desc">${m.category === 'kopi' ? 'Minuman kopi spesial dengan racikan khas Tether Brew' : 'Minuman segar pilihan non-kopi yang cocok untuk bersantai'}</div>
                        <div class="menu-card-bottom">
                            <span class="menu-card-price">${formatRupiah(m.price)}</span>
                            <a href="#maps" class="menu-card-btn relative inline-flex active:translate-y-0.5 items-center justify-center overflow-hidden text-white bg-orange-900 rounded-xl group transition-all duration-1000">
                                <span class="absolute w-0 h-0 transition-all duration-1000 ease-out bg-green-600 rounded-full group-hover:w-36 group-hover:h-36"></span>
                                <span class="absolute bottom-0 left-0 h-full -ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-auto h-full opacity-100 object-stretch" viewBox="0 0 487 487">
                                        <path fill-opacity=".15" fill-rule="nonzero" fill="#FFF" d="M0 .3c67 2.1 134.1 4.3 186.3 37 52.2 32.7 89.6 95.8 112.8 150.6 23.2 54.8 32.3 101.4 61.2 149.9 28.9 48.4 77.7 98.8 126.4 149.2H0V.3z"></path>
                                    </svg>
                                </span>
                                <span class="absolute top-0 right-0 w-12 h-full -mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="object-cover w-full h-full" viewBox="0 0 487 487">
                                        <path fill-opacity=".15" fill-rule="nonzero" fill="#FFF" d="M487 486.7c-66.1-3.6-132.3-7.3-186.3-37s-95.9-85.3-126.2-137.2c-30.4-51.8-49.3-99.9-76.5-151.4C70.9 109.6 35.6 54.8.3 0H487v486.7z"></path>
                                    </svg>
                                </span>
                                <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-green-400/20"></span>
                                <span class="relative text-base font-extrabold tracking-wide">Pesan</span>
                            </a>
                        </div>
                    </div>
                </div>
            `).join('');
        }


        // =============================================
        // BOTTOM NAV: Active state on scroll (mobile)
        // =============================================
        const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
        const sectionIds = ['home', 'maps', 'menu', 'about', 'contact'];

        function updateBottomNav() {
            let current = 'home';
            for (const id of sectionIds) {
                const section = document.getElementById(id);
                if (section) {
                    const rect = section.getBoundingClientRect();
                    if (rect.top <= window.innerHeight / 2) {
                        current = id;
                    }
                }
            }
            bottomNavItems.forEach(item => {
                item.classList.toggle('active', item.dataset.section === current);
            });
        }

        window.addEventListener('scroll', updateBottomNav, { passive: true });
    </script>
</body>
</html>





