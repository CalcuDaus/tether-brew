# Feature: Dual PWA — Customer & Rider

## Overview

Implement two separate PWA experiences within a single Laravel project, differentiated by user role:

| Aspect | PWA Customer | PWA Rider |
|--------|-------------|-----------|
| **App Name** | Tether Brew | Tether Brew Rider |
| **Short Name** | Tether Brew | TB Rider |
| **Theme Color** | `#16a34a` (green) | `#1e293b` (dark slate) |
| **Start URL** | `/` | `/dashboard` |
| **Scope** | `/` | `/dashboard`, `/rider/*` |
| **Icon** | `favicon.webp` (existing) | New rider-specific icon |
| **Service Worker** | `sw-customer.js` | `sw-rider.js` |
| **Features** | Offline menu, map cache | Background GPS sync, offline POS |

### Current State (No PWA exists)
- No `manifest.json` in `public/`
- No Service Worker registered anywhere
- No `<link rel="manifest">` in any view
- Two separate layouts: `welcome.blade.php` (customer) and `layouts/app.blade.php` (rider/admin)
- Icons exist: `favicon.webp` (logo), `tether-icon-head.webp` (logo+text)
- Rider GPS tracking already exists in `dashboard/rider.blade.php` using `navigator.geolocation.watchPosition`

---

## Scope of Changes

| # | File | Action |
|---|------|--------|
| 1 | `public/manifest-customer.json` | **CREATE** |
| 2 | `public/manifest-rider.json` | **CREATE** |
| 3 | `public/sw-customer.js` | **CREATE** |
| 4 | `public/sw-rider.js` | **CREATE** |
| 5 | `public/icons/` | **CREATE** — generate PWA icon sizes |
| 6 | `resources/views/welcome.blade.php` | **MODIFY** — add manifest link + SW registration |
| 7 | `resources/views/layouts/app.blade.php` | **MODIFY** — add manifest link + SW registration (rider only) |
| 8 | `public/offline-customer.html` | **CREATE** — offline fallback page for customer |
| 9 | `public/offline-rider.html` | **CREATE** — offline fallback page for rider |

---

## Step 1: Generate PWA Icons

Create directory `public/icons/` and generate the required icon sizes from the existing images.

### 1a. Customer Icons (from `public/favicon.webp`)

Use a tool or PHP script to resize `public/favicon.webp` into these sizes and save as PNG:

```
public/icons/customer-72x72.png
public/icons/customer-96x96.png
public/icons/customer-128x128.png
public/icons/customer-144x144.png
public/icons/customer-152x152.png
public/icons/customer-192x192.png
public/icons/customer-384x384.png
public/icons/customer-512x512.png
```

Create a one-time PHP script at `public/generate-icons.php`:

```php
<?php
// Run once: php public/generate-icons.php
// Requires GD extension

$sizes = [72, 96, 128, 144, 152, 192, 384, 512];
$iconsDir = __DIR__ . '/icons';
if (!is_dir($iconsDir)) mkdir($iconsDir, 0755, true);

// Customer icons from favicon.webp
$customerSrc = imagecreatefromwebp(__DIR__ . '/favicon.webp');
foreach ($sizes as $size) {
    $dest = imagecreatetruecolor($size, $size);
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    imagecopyresampled($dest, $customerSrc, 0, 0, 0, 0, $size, $size, imagesx($customerSrc), imagesy($customerSrc));
    imagepng($dest, "$iconsDir/customer-{$size}x{$size}.png");
    imagedestroy($dest);
}
imagedestroy($customerSrc);

// Rider icons from tether-icon-head.webp
$riderSrc = imagecreatefromwebp(__DIR__ . '/tether-icon-head.webp');
foreach ($sizes as $size) {
    $dest = imagecreatetruecolor($size, $size);
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    imagecopyresampled($dest, $riderSrc, 0, 0, 0, 0, $size, $size, imagesx($riderSrc), imagesy($riderSrc));
    imagepng($dest, "$iconsDir/rider-{$size}x{$size}.png");
    imagedestroy($dest);
}
imagedestroy($riderSrc);

echo "Icons generated successfully!\n";
```

Run: `php public/generate-icons.php`

After running, **delete** `generate-icons.php` — it's a one-time script.

---

## Step 2: Customer Manifest

**File:** `public/manifest-customer.json`

```json
{
  "name": "Tether Brew – Kopi Keliling Medan",
  "short_name": "Tether Brew",
  "description": "Temukan gerobak kopi keliling Tether Brew terdekat di Medan. Pesan kopi premium mulai Rp 8.000!",
  "start_url": "/?utm_source=pwa",
  "scope": "/",
  "display": "standalone",
  "orientation": "portrait",
  "background_color": "#f0fdf4",
  "theme_color": "#16a34a",
  "lang": "id",
  "categories": ["food", "shopping"],
  "icons": [
    { "src": "/icons/customer-72x72.png",   "sizes": "72x72",   "type": "image/png" },
    { "src": "/icons/customer-96x96.png",   "sizes": "96x96",   "type": "image/png" },
    { "src": "/icons/customer-128x128.png", "sizes": "128x128", "type": "image/png" },
    { "src": "/icons/customer-144x144.png", "sizes": "144x144", "type": "image/png" },
    { "src": "/icons/customer-152x152.png", "sizes": "152x152", "type": "image/png" },
    { "src": "/icons/customer-192x192.png", "sizes": "192x192", "type": "image/png", "purpose": "any maskable" },
    { "src": "/icons/customer-384x384.png", "sizes": "384x384", "type": "image/png" },
    { "src": "/icons/customer-512x512.png", "sizes": "512x512", "type": "image/png", "purpose": "any maskable" }
  ],
  "screenshots": [],
  "shortcuts": [
    {
      "name": "Lihat Peta Gerobak",
      "url": "/?scroll=map&utm_source=pwa",
      "icons": [{ "src": "/icons/customer-96x96.png", "sizes": "96x96" }]
    }
  ]
}
```

---

## Step 3: Rider Manifest

**File:** `public/manifest-rider.json`

```json
{
  "name": "Tether Brew Rider",
  "short_name": "TB Rider",
  "description": "Dashboard rider Tether Brew. Kelola POS, tracking GPS, dan chat pelanggan.",
  "start_url": "/dashboard?utm_source=pwa",
  "scope": "/",
  "display": "standalone",
  "orientation": "portrait",
  "background_color": "#0f172a",
  "theme_color": "#1e293b",
  "lang": "id",
  "categories": ["business", "productivity"],
  "icons": [
    { "src": "/icons/rider-72x72.png",   "sizes": "72x72",   "type": "image/png" },
    { "src": "/icons/rider-96x96.png",   "sizes": "96x96",   "type": "image/png" },
    { "src": "/icons/rider-128x128.png", "sizes": "128x128", "type": "image/png" },
    { "src": "/icons/rider-144x144.png", "sizes": "144x144", "type": "image/png" },
    { "src": "/icons/rider-152x152.png", "sizes": "152x152", "type": "image/png" },
    { "src": "/icons/rider-192x192.png", "sizes": "192x192", "type": "image/png", "purpose": "any maskable" },
    { "src": "/icons/rider-384x384.png", "sizes": "384x384", "type": "image/png" },
    { "src": "/icons/rider-512x512.png", "sizes": "512x512", "type": "image/png", "purpose": "any maskable" }
  ],
  "shortcuts": [
    {
      "name": "Buka POS",
      "url": "/rider/pos?utm_source=pwa",
      "icons": [{ "src": "/icons/rider-96x96.png", "sizes": "96x96" }]
    },
    {
      "name": "Chat Pelanggan",
      "url": "/rider/chat?utm_source=pwa",
      "icons": [{ "src": "/icons/rider-96x96.png", "sizes": "96x96" }]
    }
  ]
}
```

---

## Step 4: Customer Service Worker

**File:** `public/sw-customer.js`

```javascript
const CACHE_NAME = 'tether-customer-v1';
const OFFLINE_URL = '/offline-customer.html';

const PRECACHE_URLS = [
  '/',
  OFFLINE_URL,
  '/favicon.webp',
  '/tether-icon-head.webp',
  '/icons/customer-192x192.png',
  '/icons/customer-512x512.png',
];

// Install: precache essential assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
  );
  self.skipWaiting();
});

// Activate: clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch: Network-first for navigations, cache-first for static assets
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Skip non-GET requests
  if (request.method !== 'GET') return;

  // Navigation requests: network-first with offline fallback
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match(OFFLINE_URL))
    );
    return;
  }

  // Static assets (images, CSS, JS): cache-first
  if (
    request.destination === 'image' ||
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'font'
  ) {
    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached;
        return fetch(request).then((response) => {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
          return response;
        });
      })
    );
    return;
  }

  // API requests (e.g., /api/carts-map): network-only
  event.respondWith(fetch(request));
});
```

---

## Step 5: Rider Service Worker

**File:** `public/sw-rider.js`

```javascript
const CACHE_NAME = 'tether-rider-v1';
const OFFLINE_URL = '/offline-rider.html';

const PRECACHE_URLS = [
  '/dashboard',
  OFFLINE_URL,
  '/icons/rider-192x192.png',
  '/icons/rider-512x512.png',
];

// Install
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
  );
  self.skipWaiting();
});

// Activate
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch: network-first for all navigations
self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match(OFFLINE_URL))
    );
    return;
  }

  // Static assets: cache-first
  if (
    request.destination === 'image' ||
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'font'
  ) {
    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached;
        return fetch(request).then((response) => {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
          return response;
        });
      })
    );
    return;
  }

  event.respondWith(fetch(request));
});

// Background Sync: queue failed location updates to retry when online
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-location') {
    event.waitUntil(syncPendingLocations());
  }
});

async function syncPendingLocations() {
  // This is a placeholder. The rider dashboard already handles
  // live location via watchPosition. Background sync can be added
  // later if needed for offline location queuing.
  return Promise.resolve();
}
```

---

## Step 6: Offline Fallback Pages

### 6a. Customer Offline Page

**File:** `public/offline-customer.html`

```html
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offline - Tether Brew</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 2rem;
      color: #1e293b;
    }
    .container { max-width: 400px; }
    .icon { font-size: 4rem; margin-bottom: 1rem; }
    h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
    p { color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; }
    .btn {
      display: inline-block; padding: 0.75rem 2rem;
      background: #16a34a; color: white; border: none;
      border-radius: 12px; font-size: 1rem; font-weight: 600;
      cursor: pointer; text-decoration: none;
    }
    .btn:hover { background: #15803d; }
  </style>
</head>
<body>
  <div class="container">
    <div class="icon">☕</div>
    <h1>Kamu Sedang Offline</h1>
    <p>Koneksi internet tidak tersedia. Pastikan kamu terhubung ke Wi-Fi atau data seluler lalu coba lagi.</p>
    <button class="btn" onclick="window.location.reload()">Coba Lagi</button>
  </div>
</body>
</html>
```

### 6b. Rider Offline Page

**File:** `public/offline-rider.html`

```html
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offline - Tether Brew Rider</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', system-ui, sans-serif;
      background: #0f172a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 2rem;
      color: #f1f5f9;
    }
    .container { max-width: 400px; }
    .icon { font-size: 4rem; margin-bottom: 1rem; }
    h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
    p { color: #94a3b8; line-height: 1.6; margin-bottom: 1.5rem; }
    .btn {
      display: inline-block; padding: 0.75rem 2rem;
      background: linear-gradient(135deg, #22c55e, #16a34a);
      color: white; border: none; border-radius: 12px;
      font-size: 1rem; font-weight: 600; cursor: pointer;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="icon">📡</div>
    <h1>Koneksi Terputus</h1>
    <p>Dashboard memerlukan koneksi internet. Pastikan sinyal tersedia dan coba lagi.</p>
    <button class="btn" onclick="window.location.reload()">Coba Lagi</button>
  </div>
</body>
</html>
```

---

## Step 7: Register in Customer Layout

**File:** `resources/views/welcome.blade.php`

Find line ~101 (after favicon link, before Google Fonts):

```html
<link rel="icon" type="image/webp" href="{{ asset('tether-icon-head.webp') }}">
<link rel="apple-touch-icon" href="{{ asset('tether-icon-head.webp') }}">
```

Add directly **after** those two lines:

```html
<link rel="manifest" href="/manifest-customer.json">
<meta name="theme-color" content="#16a34a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Tether Brew">
```

Then, find the closing `</body>` tag (around line 700+) and add **before** it:

```html
<script>
// Register Customer Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw-customer.js', { scope: '/' })
            .then(reg => console.log('Customer SW registered:', reg.scope))
            .catch(err => console.log('Customer SW failed:', err));
    });
}

// Custom Install Prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Show install banner after 30 seconds
    setTimeout(() => {
        if (!deferredPrompt) return;
        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.innerHTML = `
            <div style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#16a34a;color:white;padding:14px 24px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);z-index:99999;display:flex;align-items:center;gap:12px;font-family:Inter,sans-serif;max-width:90%;animation:slideUp .4s ease;">
                <span style="font-size:1.5rem;">☕</span>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:0.95rem;">Install Tether Brew</div>
                    <div style="font-size:0.8rem;opacity:0.9;">Akses cepat tanpa buka browser</div>
                </div>
                <button onclick="installPWA()" style="background:white;color:#16a34a;border:none;padding:8px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;">Install</button>
                <button onclick="this.closest('#pwa-install-banner').remove()" style="background:none;border:none;color:white;font-size:1.2rem;cursor:pointer;padding:4px;">✕</button>
            </div>
        `;
        document.body.appendChild(banner);
    }, 30000);
});

async function installPWA() {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log('Install outcome:', outcome);
    deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.remove();
}
</script>
<style>
@keyframes slideUp {
    from { opacity: 0; transform: translateX(-50%) translateY(20px); }
    to { opacity: 1; transform: translateX(-50%) translateY(0); }
}
</style>
```

---

## Step 8: Register in Rider Layout

**File:** `resources/views/layouts/app.blade.php`

Find line ~6-7 (inside `<head>`, after csrf-token meta):

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') - Tether Brew</title>
```

Add directly **after** the title tag:

```html
@if(auth()->check() && auth()->user()->isRider())
    <link rel="manifest" href="/manifest-rider.json">
    <meta name="theme-color" content="#1e293b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TB Rider">
    <link rel="apple-touch-icon" href="/icons/rider-192x192.png">
@endif
```

Then, find line ~367 (just before `@stack('scripts')`):

```html
@endif
    @stack('scripts')
```

Add this block **before** `@stack('scripts')`:

```html
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

    // Show install banner after 10 seconds (riders benefit more from install)
    setTimeout(() => {
        if (!riderDeferredPrompt) return;
        const banner = document.createElement('div');
        banner.id = 'pwa-rider-install';
        banner.innerHTML = `
            <div style="position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#1e293b,#334155);color:white;padding:14px 24px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.3);z-index:99999;display:flex;align-items:center;gap:12px;font-family:Inter,sans-serif;max-width:90%;border:1px solid rgba(255,255,255,0.1);animation:riderSlideUp .4s ease;">
                <span style="font-size:1.5rem;">🏍️</span>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:0.95rem;">Install TB Rider</div>
                    <div style="font-size:0.8rem;opacity:0.8;">Akses POS & tracking lebih cepat</div>
                </div>
                <button onclick="installRiderPWA()" style="background:linear-gradient(135deg,#22c55e,#16a34a);color:white;border:none;padding:8px 16px;border-radius:10px;font-weight:700;font-size:0.85rem;cursor:pointer;">Install</button>
                <button onclick="this.closest('#pwa-rider-install').remove()" style="background:none;border:none;color:white;font-size:1.2rem;cursor:pointer;padding:4px;">✕</button>
            </div>
        `;
        document.body.appendChild(banner);
    }, 10000);
});

async function installRiderPWA() {
    if (!riderDeferredPrompt) return;
    riderDeferredPrompt.prompt();
    const { outcome } = await riderDeferredPrompt.userChoice;
    console.log('Rider install outcome:', outcome);
    riderDeferredPrompt = null;
    const banner = document.getElementById('pwa-rider-install');
    if (banner) banner.remove();
}
</script>
<style>
@keyframes riderSlideUp {
    from { opacity: 0; transform: translateX(-50%) translateY(20px); }
    to { opacity: 1; transform: translateX(-50%) translateY(0); }
}
</style>
@endif
```

Note: `bottom:80px` accounts for the rider's bottom navigation bar.

---

## Step 9: Prevent SW Conflict

Since both service workers register with `scope: '/'`, a rider who visits the landing page could get the customer SW. To prevent this, add a role check at the top of each SW fetch handler.

**Alternative approach (simpler):** In `sw-customer.js`, skip caching for `/dashboard*` and `/rider/*` paths:

In `sw-customer.js`, at the top of the `fetch` event listener, add:

```javascript
// Don't intercept rider/dashboard routes
const url = new URL(request.url);
if (url.pathname.startsWith('/dashboard') || url.pathname.startsWith('/rider/')) return;
```

In `sw-rider.js`, add similarly:

```javascript
// Only intercept dashboard and rider routes, plus static assets
const url = new URL(request.url);
const isRiderRoute = url.pathname.startsWith('/dashboard') || url.pathname.startsWith('/rider/') || url.pathname.startsWith('/login');
const isStaticAsset = ['image', 'style', 'script', 'font'].includes(request.destination);
if (!isRiderRoute && !isStaticAsset && request.mode === 'navigate') return;
```

---

## Step 10: Verify

After implementing all changes:

1. **Generate icons:** Run `php public/generate-icons.php`, then delete the script.
2. **Clear browser cache** and reload both the landing page and rider dashboard.
3. **Customer PWA test:**
   - Open `/` in Chrome DevTools → Application → Manifest. Verify name, icons, theme_color.
   - Check Application → Service Workers shows `sw-customer.js` active.
   - Wait 30 seconds for the install banner to appear.
   - Click "Install" — verify standalone app opens with correct name and icon.
   - Turn off network (DevTools → offline) — verify offline fallback page shows.
4. **Rider PWA test:**
   - Login as a rider, go to `/dashboard`.
   - DevTools → Application → Manifest. Verify "Tether Brew Rider" with dark theme.
   - Check Service Workers shows `sw-rider.js`.
   - Wait 10 seconds for rider install banner (above bottom nav).
   - Install — verify standalone app opens to dashboard with rider icon.
   - Test offline fallback.
5. **Conflict test:**
   - Install customer PWA → open rider dashboard inside it → should work normally.
   - Install rider PWA → navigate to `/` → should work normally.

---

## Important Notes

- **HTTPS required:** Service Workers only work on HTTPS (or localhost for development). The production site must be served over HTTPS.
- **Manifest must be on same origin** as the pages it covers. Both manifests are in `public/`, served from the same domain.
- **Only one manifest per page load.** The `<link rel="manifest">` tag determines which manifest the browser uses. Customer pages link to `manifest-customer.json`, rider pages link to `manifest-rider.json`.
- **Install prompt only fires once** per manifest per browser. If the user dismisses it, the browser won't show it again for a while. The custom banner handles this gracefully.
- **Cache versioning:** When deploying updates, increment `CACHE_NAME` (e.g., `tether-customer-v2`) to force the old cache to be purged.
- **Icon sizes:** The minimum required sizes for Chrome are 192x192 and 512x512 with `purpose: "any maskable"`. The additional sizes improve rendering across devices.
