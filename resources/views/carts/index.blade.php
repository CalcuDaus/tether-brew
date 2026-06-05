@extends('layouts.app')
@section('title', 'Kelola Gerobak')

@section('actions')
    <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Gerobak</button>
@endsection

@section('content')
<div x-data="cartModal()" @open-modal.window="openModal($event.detail)">
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
                                        <span class="text-xs-green">ðŸ“ {{ number_format($cart->location->latitude, 4) }}, {{ number_format($cart->location->longitude, 4) }}</span>
                                    @else
                                        <span class="text-sm-muted">Belum diset</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex-gap-2">
                                        <button type="button" @click="$dispatch('open-modal', { type: 'edit', cart: {{ json_encode(['id' => $cart->id, 'name' => $cart->name, 'description' => $cart->description, 'user_id' => $cart->user_id, 'status' => $cart->status, 'latitude' => $cart->location ? $cart->location->latitude : '', 'longitude' => $cart->location ? $cart->location->longitude : '']) }} })" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</button>
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
                <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Gerobak</button>
            </div>
        @endif
    </div>
</div>

    <!-- Modal -->
    <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 transition-opacity" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto" style="position: fixed; inset: 0; z-index: 10; overflow-y: auto;">
            <div class="flex items-center justify-center min-h-full p-4 text-center" style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem;">
                <!-- Modal Panel -->
                <div x-show="isOpen" @click.away="closeModal()"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="card max-w-640 text-left transform transition-all" style="width: 100%; max-width: 640px; box-shadow: var(--shadow-lg); text-align: left;">
                    
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title" x-text="type === 'add' ? 'Tambah Gerobak' : 'Edit Gerobak'"></h3>
                        <button type="button" @click="closeModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; line-height:1; color: var(--text-muted);">&times;</button>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" :action="formAction">
                            @csrf
                            <template x-if="type === 'edit'">
                                @method('PUT')
                            </template>

                            <div class="form-group">
                                <label class="form-label" for="name">Nama Gerobak *</label>
                                <input type="text" id="name" name="name" class="form-input" x-model="cart.name" placeholder="Gerobak Kopi Pak Budi" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="description">Deskripsi</label>
                                <textarea id="description" name="description" class="form-input form-textarea" x-model="cart.description" placeholder="Deskripsi gerobak..."></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="user_id">Rider</label>
                                <select id="user_id" name="user_id" class="form-input form-select" x-model="cart.user_id">
                                    <option value="">-- Pilih Rider --</option>
                                    @foreach($riders as $rider)
                                        <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="status">Status *</label>
                                <select id="status" name="status" class="form-input form-select" x-model="cart.status" required>
                                    <option value="inactive">Inactive</option>
                                    <option value="active">Active</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label" for="latitude">Latitude</label>
                                    <input type="text" id="latitude" name="latitude" class="form-input" x-model="cart.latitude" placeholder="-6.2088">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="longitude">Longitude</label>
                                    <input type="text" id="longitude" name="longitude" class="form-input" x-model="cart.longitude" placeholder="106.8456">
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                                <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-primary" x-text="type === 'add' ? 'Simpan Gerobak' : 'Update Gerobak'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function cartModal() {
        return {
            isOpen: false,
            type: 'add',
            formAction: '{{ route('carts.store') }}',
            cart: {
                id: '',
                name: '',
                description: '',
                user_id: '',
                status: 'inactive',
                latitude: '',
                longitude: ''
            },
            openModal(detail) {
                this.type = detail.type;
                if (this.type === 'edit') {
                    this.cart = detail.cart;
                    this.formAction = `/carts/${this.cart.id}`;
                } else {
                    this.cart = { id: '', name: '', description: '', user_id: '', status: 'inactive', latitude: '', longitude: '' };
                    this.formAction = '{{ route('carts.store') }}';
                }
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
            }
        }
    }
</script>
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
                                    <a href="${cart.edit_url}" class="popup-link">Edit Gerobak â†’</a>
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

