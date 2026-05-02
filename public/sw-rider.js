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

  // Only intercept dashboard and rider routes, plus static assets
  const url = new URL(request.url);
  const isRiderRoute = url.pathname.startsWith('/dashboard') || url.pathname.startsWith('/rider/') || url.pathname.startsWith('/login');
  const isStaticAsset = ['image', 'style', 'script', 'font'].includes(request.destination);
  if (!isRiderRoute && !isStaticAsset && request.mode === 'navigate') return;

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match(OFFLINE_URL))
    );
    return;
  }

  // Static assets: cache-first
  if (isStaticAsset) {
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

// Background Sync placeholder
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-location') {
    event.waitUntil(Promise.resolve());
  }
});
