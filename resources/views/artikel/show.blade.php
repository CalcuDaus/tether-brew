<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $artikel->title }} â€“ Tether Brew</title>
    <meta name="description" content="{{ $artikel->excerpt }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/artikel/' . $artikel->slug) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $artikel->title }} â€“ Tether Brew">
    <meta property="og:description" content="{{ $artikel->excerpt }}">
    <meta property="og:url" content="{{ url('/artikel/' . $artikel->slug) }}">
    <meta property="og:image" content="{{ $artikel->cover_image ? asset('storage/' . $artikel->cover_image) : asset('favicon.webp') }}">

    <link rel="icon" type="image/webp" href="{{ asset('tether-icon-head.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/landing.css'])
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
                localStorage.setItem('theme', document.documentElement.classList.contains('light-theme') ? 'light' : 'dark');
                return;
            }

            const transition = document.startViewTransition(() => {
                if (callback) callback();
                document.documentElement.classList.toggle('light-theme');
                localStorage.setItem('theme', document.documentElement.classList.contains('light-theme') ? 'light' : 'dark');
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
<body x-data="{ darkMode: (localStorage.getItem('theme') || 'light') !== 'light' }">

    {{-- ===== SIMPLE TOP BAR ===== --}}
    <div class="artikel-page-topbar">
        <a href="{{ route('artikel.index') }}" class="artikel-back-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Semua Artikel
        </a>
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

    {{-- ===== ARTICLE DETAIL ===== --}}
    <article class="artikel-detail">
        {{-- Hero Image --}}
        <div class="artikel-detail-hero">
            <img src="{{ $artikel->cover_image ? asset('storage/' . $artikel->cover_image) : '/storage/image/kopi-tether-new.webp' }}" alt="{{ $artikel->title }}" loading="lazy">
            <div class="artikel-detail-hero-overlay"></div>
            <div class="artikel-detail-hero-content">
                <span class="artikel-card-category">{{ $artikel->category }}</span>
                <h1 class="artikel-detail-title">{{ $artikel->title }}</h1>
                <div class="artikel-detail-meta">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>{{ $artikel->published_at ? $artikel->published_at->translatedFormat('d F Y') : $artikel->created_at->translatedFormat('d F Y') }}</span>
                    <span class="artikel-detail-meta-dot">Â·</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>{{ $artikel->read_time }} menit baca</span>
                </div>
            </div>
        </div>

        {{-- Article Body --}}
        <div class="artikel-detail-body">
            <div class="artikel-detail-content">
                {!! $artikel->content !!}
            </div>

            {{-- Share & Back --}}
            <div class="artikel-detail-footer">
                <a href="{{ route('artikel.index') }}" class="btn-hero btn-hero-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Kembali ke Daftar Artikel
                </a>
                <a href="{{ route('landing') }}" class="btn-hero btn-hero-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Beranda
                </a>
            </div>
        </div>
    </article>

    {{-- ===== FOOTER ===== --}}
    <footer class="footer">
        <p>Â© {{ date('Y') }} Tether Brew.</p>
    </footer>

</body>
</html>
