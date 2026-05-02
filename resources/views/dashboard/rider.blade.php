@extends('layouts.app')
@section('title', 'Dashboard Rider')

@section('content')
@if($cart)
    <div class="rider-dashboard-wrapper">
    {{-- Cart Info & Location --}}
    <div class="stats-grid rider-stats-section">
        <div class="stat-card">
            <div class="stat-icon gold">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                </svg>
            </div>
            <div class="stat-value">{{ $cart->name }}</div>
            <div class="stat-label">Gerobak Anda</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="8"/><line x1="12" x2="12" y1="16" y2="8"/><line x1="10" x2="14" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="14"/>
                </svg>
            </div>
            <div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            <div class="stat-label">Pendapatan Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div class="stat-value">{{ $todayCount }}</div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon {{ $cart->status === 'active' ? 'green' : 'red' }}">
                @if($cart->status === 'active')
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                @else
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                @endif
            </div>
            <div class="stat-value text-lg-capitalize">{{ $cart->status }}</div>
            <div class="stat-label">Status Gerobak</div>
        </div>
    </div>

    <div class="grid-2 rider-tracking-section">
        {{-- Live Auto-Tracking --}}
        <div class="card">
            <div class="card-header flex-between">
                <h3 class="card-title">📍 Live Tracking</h3>
                <div class="flex-gap-2">
                    <button id="simulation-toggle" onclick="toggleSimulation()" class="btn btn-secondary btn-sm" title="Gunakan jika GPS diblokir browser/HTTP">
                        Mode Simulasi: OFF
                    </button>
                    <div id="tracking-badge" class="badge-red-sm">
                        ⏹ Nonaktif
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- Toggle & Status --}}
                <div class="flex-between-panel">
                    <div class="flex-1">
                        <div class="text-primary-semi-mb-0">Auto-Tracking GPS</div>
                        <div id="tracking-status-text" class="text-sm-muted">Klik tombol untuk mulai memancarkan lokasi</div>
                        <div id="secure-origin-warning" class="text-xs text-red-500 mt-1 hidden">
                            ⚠️ GPS diblokir (Butuh HTTPS). Gunakan <b>Mode Simulasi</b> di atas.
                        </div>
                    </div>
                    <button id="tracking-toggle" onclick="toggleTracking()" class="btn btn-primary min-w-120-center ml-4">
                        🟢 Mulai
                    </button>
                </div>

                {{-- Mini Map --}}
                <div class="map-container-wrapper">
                    <div id="rider-map" class="map-container-rider"></div>
                </div>

                {{-- Live Info --}}
                <div class="grid-2-gap">
                    <div class="coord-box">
                        <div class="coord-label">Latitude</div>
                        <div id="live-lat" class="coord-value">{{ $cart->location?->latitude ?? '-' }}</div>
                    </div>
                    <div class="coord-box">
                        <div class="coord-label">Longitude</div>
                        <div id="live-lng" class="coord-value">{{ $cart->location?->longitude ?? '-' }}</div>
                    </div>
                </div>

                {{-- Log --}}
                <div id="tracking-log" class="tracking-log-box">
                    @if($cart->location)
                        <div><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Lokasi terakhir: {{ $cart->location->latitude }}, {{ $cart->location->longitude }} · {{ $cart->location->updated_at->diffForHumans() }}</div>
                    @else
                        <div>⏳ Menunggu data lokasi...</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stock Overview --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                    </svg> Stok Gerobak
                </h3>
            </div>
            <div class="card-body card-body-no-padding">
                @if($cart->inventories->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart->inventories as $inv)
                                <tr>
                                    <td class="text-primary-semi">{{ $inv->product->name }}</td>
                                    <td>{{ $inv->stock }}</td>
                                    <td>
                                        @if($inv->stock <= 0)
                                            <span class="badge badge-closed">Habis</span>
                                        @elseif($inv->stock <= 5)
                                            <span class="badge badge-warning">Menipis</span>
                                        @else
                                            <span class="badge badge-active">Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state p-5-custom">
                        <div class="empty-state-icon">
                            <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                            </svg>
                        </div>
                        <div class="empty-state-text">Belum ada stok. Hubungi admin.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Today's Transactions --}}
    <div class="card mt-5-custom rider-transactions-section">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg> Transaksi Hari Ini
            </h3>
            <a href="{{ route('rider.pos') }}" class="btn btn-primary btn-sm">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg> Buat Transaksi
            </a>
        </div>
        <div class="card-body card-body-no-padding">
            @if(count($todayTransactions) > 0)
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Bayar</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayTransactions as $tx)
                                <tr>
                                    <td>#{{ $tx->id }}</td>
                                    <td>
                                        @foreach($tx->items as $item)
                                            <span class="text-sm-muted">{{ $item->product->name }} ({{ $item->qty }})</span>@if(!$loop->last), @endif
                                        @endforeach
                                    </td>
                                    <td class="text-gold-semi">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                    <td><span class="badge badge-{{ $tx->payment_method }}">{{ strtoupper($tx->payment_method) }}</span></td>
                                    <td>{{ $tx->created_at->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></div>
                    <div class="empty-state-title">Belum ada transaksi hari ini</div>
                    <a href="{{ route('rider.pos') }}" class="btn btn-primary"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg> Buat Transaksi Pertama</a>
                </div>
            @endif
        </div>
    </div>
    </div>
@else
    <div class="empty-state p-80-20">
        <div class="empty-state-icon"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg></div>
        <div class="empty-state-title">Belum Memiliki Gerobak</div>
        <div class="empty-state-text">Hubungi admin untuk mendapatkan gerobak.</div>
    </div>
@endif
@endsection

@push('scripts')
@if(isset($cart) && $cart)
<script>
    // =============================================
    // LIVE AUTO-TRACKING SYSTEM
    // =============================================
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    const LIVE_URL = "{{ route('rider.location.live') }}";
    const IS_LOCAL = {{ app()->environment('local') ? 'true' : 'false' }};
    const MOCK_LAT = 3.5395706;
    const MOCK_LNG = 98.6704442;

    let isTracking = false;
    let isSimulation = false;
    let watchId = null;
    let sendCount = 0;
    let riderMap = null;
    let riderMarker = null;

    // Initialize mini map
    const initLat = {{ $cart->location?->latitude ?? 3.5395706 }};
    const initLng = {{ $cart->location?->longitude ?? 98.6704442 }};

    riderMap = L.map('rider-map', { zoomControl: false, attributionControl: false }).setView([initLat, initLng], 16);
    
    // Theme-aware map tiles
    const darkTiles = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
    const lightTiles = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
    
    let activeTileLayer = L.tileLayer((localStorage.getItem('theme') || 'light') === 'light' ? lightTiles : darkTiles, {
        maxZoom: 19
    }).addTo(riderMap);

    window.addEventListener('theme-changed', (e) => {
        riderMap.removeLayer(activeTileLayer);
        activeTileLayer = L.tileLayer(e.detail.theme === 'light' ? lightTiles : darkTiles, {
            maxZoom: 19
        }).addTo(riderMap);
    });

    // Custom rider marker icon
    const riderIcon = L.icon({
        iconUrl: '{{ asset("custom_icon_maps.png") }}',
        iconSize: [40, 40], 
        iconAnchor: [20, 40]
    });

    riderMarker = L.marker([initLat, initLng], { 
        icon: riderIcon,
        draggable: false 
    }).addTo(riderMap);

    // Simulation event: update coordinates on drag
    riderMarker.on('dragend', function(e) {
        if (isSimulation) {
            const pos = riderMarker.getLatLng();
            updateUI(pos.lat, pos.lng);
            sendLocation(pos.lat, pos.lng);
            addLog(`🕹️ Simulasi: Marker dipindah ke ${pos.lat.toFixed(5)}, ${pos.lng.toFixed(5)}`);
        }
    });

    function toggleSimulation() {
        if (isTracking && !isSimulation) {
            alert('Matikan GPS Tracking dulu sebelum mengaktifkan simulasi.');
            return;
        }

        isSimulation = !isSimulation;
        const btn = document.getElementById('simulation-toggle');
        
        if (isSimulation) {
            btn.innerHTML = 'Mode Simulasi: ON';
            btn.classList.replace('btn-secondary', 'btn-gold');
            riderMarker.dragging.enable();
            addLog('🛠️ Mode Simulasi AKTIF. Anda bisa menggeser marker di peta secara manual.');
            
            // Auto start visual tracking if not active
            if (!isTracking) {
                document.getElementById('tracking-badge').innerHTML = '<span class="status-indicator-dot" style="background:#fbbf24"></span> Simulasi';
                document.getElementById('tracking-badge').style.color = '#fbbf24';
            }
        } else {
            btn.innerHTML = 'Mode Simulasi: OFF';
            btn.classList.replace('btn-gold', 'btn-secondary');
            riderMarker.dragging.disable();
            addLog('⏹ Mode Simulasi MATI.');
            
            if (!isTracking) {
                stopTrackingUI(); // Reset UI
            }
        }
    }

    function addLog(message) {
        const log = document.getElementById('tracking-log');
        const time = new Date().toLocaleTimeString('id-ID');
        const entry = document.createElement('div');
        entry.textContent = `[${time}] ${message}`;
        log.prepend(entry);
        // Keep max 50 entries
        while (log.children.length > 50) log.removeChild(log.lastChild);
    }

    function updateUI(lat, lng) {
        document.getElementById('live-lat').textContent = lat.toFixed(7);
        document.getElementById('live-lng').textContent = lng.toFixed(7);
        riderMarker.setLatLng([lat, lng]);
        riderMap.panTo([lat, lng]);
    }

    async function sendLocation(lat, lng, status = null) {
        try {
            const bodyData = { latitude: lat, longitude: lng };
            if (status) bodyData.status = status;

            const res = await fetch(LIVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(bodyData),
            });
            const data = await res.json();
            if (data.success) {
                sendCount++;
                addLog(`<svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Lokasi terkirim #${sendCount} → ${lat.toFixed(5)}, ${lng.toFixed(5)}`);
                // Update badge if status active
                if (status === 'active') {
                    const statusVal = document.querySelector('.rider-stats-section .stat-card:nth-child(4) .stat-value');
                    if(statusVal) statusVal.textContent = 'active';
                } else if (status === 'inactive') {
                    const statusVal = document.querySelector('.rider-stats-section .stat-card:nth-child(4) .stat-value');
                    if(statusVal) statusVal.textContent = 'inactive';
                }
            } else {
                addLog(`⚠️ Server error: ${JSON.stringify(data)}`);
            }
        } catch (err) {
            addLog(`❌ Gagal kirim: ${err.message}`);
        }
    }

    function toggleTracking() {
        if (isTracking) {
            stopTracking();
        } else {
            startTracking();
        }
    }

    function startTracking() {
        if (isSimulation) {
            addLog('💡 Matikan Mode Simulasi sebelum memulai GPS Realtime.');
            return;
        }

        if (!navigator.geolocation && !IS_LOCAL) {
            addLog('❌ Browser tidak mendukung Geolocation');
            return;
        }

        isTracking = true;
        const btn = document.getElementById('tracking-toggle');
        const badge = document.getElementById('tracking-badge');
        const statusText = document.getElementById('tracking-status-text');
        const warn = document.getElementById('secure-origin-warning');

        btn.innerHTML = '🔴 Berhenti';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-danger');

        badge.style.background = 'rgba(76,175,80,0.15)';
        badge.style.color = '#22c55e';
        badge.innerHTML = '<span class="status-indicator-dot"></span> Aktif';

        statusText.textContent = 'GPS sedang memancarkan lokasi secara otomatis...';
        statusText.style.color = '#22c55e';
        warn.classList.add('hidden');

        // Development Mock vs Production Real
        if (IS_LOCAL) {
            addLog('🟢 Live tracking DIMULAI (Mode Development - Mock Location)');
            updateUI(MOCK_LAT, MOCK_LNG);
            sendLocation(MOCK_LAT, MOCK_LNG, 'active');
            
            watchId = setInterval(() => {
                sendLocation(MOCK_LAT, MOCK_LNG);
                addLog(`📡 Mock GPS: ${MOCK_LAT.toFixed(5)}, ${MOCK_LNG.toFixed(5)}`);
            }, 8000);
        } else {
            addLog('🟢 Live tracking DIMULAI (Production)');
            watchId = navigator.geolocation.watchPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const accuracy = pos.coords.accuracy;
                    updateUI(lat, lng);
                    
                    // On first update, set status active. Subsequent updates just send coords.
                    const isFirst = (sendCount === 0); 
                    sendLocation(lat, lng, isFirst ? 'active' : null);
                    
                    addLog(`📡 GPS: ${lat.toFixed(5)}, ${lng.toFixed(5)} (akurasi: ${accuracy.toFixed(0)}m)`);
                },
                (err) => {
                    addLog(`❌ GPS Error: ${err.message}`);
                    if (err.message.includes('secure origins') || err.code === 1) {
                        warn.classList.remove('hidden');
                    }
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 0, 
                    timeout: 10000,
                }
            );
        }
    }

    function stopTracking() {
        if (IS_LOCAL && watchId !== null) {
            clearInterval(watchId);
        } else if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
        }
        watchId = null;
        isTracking = false;

        stopTrackingUI();

        // Optional: send inactive status when stopped
        const lastLat = document.getElementById('live-lat').textContent;
        const lastLng = document.getElementById('live-lng').textContent;
        if (lastLat !== '-' && lastLng !== '-') {
            sendLocation(parseFloat(lastLat), parseFloat(lastLng), 'inactive');
        }
        
        addLog(`🔴 Live tracking DIHENTIKAN (total ${sendCount} update terkirim)`);
    }

    function stopTrackingUI() {
        const btn = document.getElementById('tracking-toggle');
        const badge = document.getElementById('tracking-badge');
        const statusText = document.getElementById('tracking-status-text');

        btn.innerHTML = '🟢 Mulai';
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-primary');

        badge.style.background = 'rgba(239, 83, 80, 0.15)';
        badge.style.color = '#ef5350';
        badge.innerHTML = '⏹ Nonaktif';

        statusText.textContent = 'Klik tombol untuk mulai memancarkan lokasi';
        statusText.style.color = 'var(--text-muted)';
    }

    // Fix map rendering issue inside hidden/tab containers
    setTimeout(() => { riderMap.invalidateSize(); }, 300);
</script>
@endif
@endpush

