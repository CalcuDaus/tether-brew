<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Customer - Tether Brew</title>
    <link rel="icon" type="image/webp" href="{{ asset('tether-icon-head.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/landing.css'])
    <style>
        :root {
            --auth-bg: #f1f5f9; /* Light mode bg */
            --card-bg: #ffffff; /* Solid white card */
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            background: var(--auth-bg);
            color: #334155;
            padding: 1.5rem;
            margin: 0;
            overflow-x: hidden;
        }
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 800px;
            display: flex;
            background: var(--card-bg);
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
        }
        .auth-image-side {
            flex: 1;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border-right: 1px solid #e2e8f0;
        }
        .auth-image-side img {
            width: 100%;
            max-width: 240px;
            height: auto;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.08));
            /* Animation removed */
        }
        .auth-form-side {
            flex: 1.2;
            padding: 2rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--card-bg);
        }
        .auth-form-side .brand {
            margin-bottom: 1.5rem;
        }
        .auth-form-side .brand h1 {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }
        .auth-form-side .brand p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-top: 0.3rem;
        }
        .auth-field {
            margin-bottom: 1rem;
        }
        .auth-field label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            opacity: 0.9;
            color: #475569;
        }
        .auth-field input {
            width: 100%;
            padding: 0.75rem 1.1rem;
            border: 1px solid #cbd5e1;
            border-radius: 50px;
            background: #f8fafc;
            color: #1e293b;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .auth-field input:focus {
            outline: none;
            border-color: #22c55e;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
        }
        
        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .auth-link a {
            color: #16a34a;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-link a:hover {
            text-decoration: underline;
        }
        .auth-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 0.7rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .auth-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #64748b;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }
        .auth-back:hover {
            color: #1e293b;
        }

        @media (max-width: 850px) {
            .auth-container {
                flex-direction: column;
                max-width: 500px;
            }
            .auth-image-side {
                display: none;
            }
            .auth-form-side {
                padding: 2rem 2rem;
            }
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <div class="auth-container">
        <div class="auth-image-side">
            <img src="{{ asset('tether-icon-head.webp') }}" alt="Tether Brew">
        </div>
        <div class="auth-form-side">
            <a href="/" class="auth-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                Kembali
            </a>
            <div class="brand">
                <h1>Daftar Baru</h1>
                <p>Buat akun untuk mulai chat dengan rider</p>
            </div>

            @if($errors->any())
                <div class="auth-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('customer.register') }}">
                @csrf
                <div class="auth-field">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required autofocus>
                </div>
                <div class="auth-field">
                    <label for="phone">Nomor HP</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="auth-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="auth-field">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="relative inline-flex active:translate-y-0.5 items-center justify-center overflow-hidden text-white bg-orange-900 rounded-full group transition-all duration-1000" style="width: 100%; padding: 0.75rem 1.1rem; border: none; cursor: pointer; font-family: inherit; margin-top: 1rem;">
                    <span class="absolute w-0 h-0 transition-all duration-1000 ease-out bg-green-600 rounded-full group-hover:w-[200%] group-hover:h-[200%] group-hover:left-[-50%] group-hover:top-[-50%]"></span>
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
                    <span class="relative font-bold z-10 text-[15px]">Daftar Sekarang</span>
                </button>
            </form>

            <div class="auth-link">
                Sudah punya akun? <a href="{{ route('customer.login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 60, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#22c55e" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.3, "random": true },
                "size": { "value": 3, "random": true },
                "line_linked": { "enable": true, "distance": 150, "color": "#22c55e", "opacity": 0.2, "width": 1 },
                "move": { "enable": true, "speed": 2, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": false }, "resize": true }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>
