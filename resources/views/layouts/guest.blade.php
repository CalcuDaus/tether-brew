<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tether Brew')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'light') {
                document.documentElement.classList.add('light-theme');
            }
        })();
    </script>
</head>
<body>
    <div class="auth-container">
        <div class="auth-brand">
            <div class="auth-brand-icon">
                <img src="{{ asset('tether-icon-head.webp') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="auth-brand-title">Tether Brew</div>
            <div class="auth-brand-sub">Management System</div>
        </div>

        @yield('content')
    </div>
</body>
</html>

