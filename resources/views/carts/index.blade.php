@extends('layouts.app')
@section('title', 'Kelola Gerobak')

@section('actions')
    <a href="{{ route('carts.create') }}" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Gerobak</a>
@endsection

@section('content')
<div class="admin-map-card">
    <div class="map-overlay-title">
        <div class="map-pulse"></div>
        Live Tracking Gerobak
    </div>
    <div id="admin-carts-map"></div>
</div>

<div class="card">
    <div class="card-body card-body-no-padding">
        @if($carts->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Rider</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carts as $cart)
                            <tr>
                                <td>#{{ $cart->id }}</td>
                                <td class="text-primary-semi"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg> {{ $cart->name }}</td>
                                <td>{{ $cart->user?->name ?? '-' }}</td>
                                <td>
                                    <button
                                        class="badge badge-{{ $cart->status }} status-toggle"
                                        data-cart-id="{{ $cart->id }}"
                                        data-url="{{ route('carts.toggle_status', $cart) }}"
                                        onclick="toggleCartStatus(this)"
                                        style="cursor: pointer; border: none; font-family: inherit; transition: all 0.3s;"
                                        title="Klik untuk ubah status"
                                    >{{ ucfirst($cart->status) }}</button>
                                </td>
                                <td>
                                    @if($cart->location)
                                        <span class="text-xs-green">📍 {{ number_format($cart->location->latitude, 4) }}, {{ number_format($cart->location->longitude, 4) }}</span>
                                    @else
                                        <span class="text-sm-muted">Belum diset</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex-gap-2">
                                        <a href="{{ route('carts.edit', $cart) }}" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</a>
                                        <form method="POST" action="{{ route('carts.destroy', $cart) }}" onsubmit="return confirm('Yakin hapus gerobak ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4-custom">
                {{ $carts->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg></div>
                <div class="empty-state-title">Belum Ada Gerobak</div>
                <div class="empty-state-text">Mulai tambahkan gerobak pertama Anda</div>
                <a href="{{ route('carts.create') }}" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Gerobak</a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('admin-carts-map', { attributionControl: false }).setView([3.59, 98.67], 13);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 20,
            attribution: ''
        }).addTo(map);

        const markers = {};

        const cartIcon = L.icon({
            iconUrl: '{{ asset("custom_icon_maps.png") }}',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        async function updateMap() {
            try {
                const response = await fetch("{{ route('carts.map_data') }}");
                const carts = await response.json();
                
                const currentIds = new Set(carts.map(c => c.id));
                const bounds = L.latLngBounds();

                carts.forEach(cart => {
                    const pos = [cart.latitude, cart.longitude];
                    bounds.extend(pos);

                    if (markers[cart.id]) {
                        markers[cart.id].setLatLng(pos);
                    } else {
                        markers[cart.id] = L.marker(pos, { icon: cartIcon })
                            .addTo(map)
                            .bindPopup(`
                                <div class="marker-popup">
                                    <strong style="display:block;margin-bottom:4px">${cart.name}</strong>
                                    <div style="font-size:12px;opacity:0.8">Rider: ${cart.rider}</div>
                                    <div style="font-size:12px;opacity:0.8;margin-top:2px">Status: <span class="badge badge-${cart.status}">${cart.status}</span></div>
                                    <a href="${cart.edit_url}" class="popup-link">Edit Gerobak →</a>
                                </div>
                            `);
                    }
                });

                // Remove markers for carts no longer in data
                Object.keys(markers).forEach(id => {
                    if (!currentIds.has(parseInt(id))) {
                        map.removeLayer(markers[id]);
                        delete markers[id];
                    }
                });

                if (carts.length > 0 && map.getZoom() < 13) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            } catch (error) {
                console.error('Error updating map:', error);
            }
        }

        // Initial load
        updateMap();
        
        // Polling every 15 seconds
        setInterval(updateMap, 15000);
    });

    async function toggleCartStatus(btn) {
        const url = btn.dataset.url;
        const originalText = btn.textContent;
        btn.textContent = '...';
        btn.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (data.success) {
                btn.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                btn.className = `badge badge-${data.status} status-toggle`;
            } else {
                btn.textContent = originalText;
            }
        } catch (error) {
            console.error('Toggle failed:', error);
            btn.textContent = originalText;
        }

        btn.disabled = false;
    }
</script>
@endpush

