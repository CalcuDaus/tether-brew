@extends('layouts.app')
@section('title', 'Kelola Rider')

@section('actions')
    <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Rider</button>
@endsection

@section('content')
<div x-data="riderModal()" @open-modal.window="openModal($event.detail)">
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($riders->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Gerobak</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riders as $rider)
                            <tr>
                                <td>#{{ $rider->id }}</td>
                                <td class="text-primary-semi">
                                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg> {{ $rider->name }}
                                </td>
                                <td>{{ $rider->email }}</td>
                                <td>
                                    @if($rider->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $rider->whatsapp) }}" target="_blank" class="text-xs-green">
                                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.15em;">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                            </svg>
                                            {{ $rider->whatsapp }}
                                        </a>
                                    @else
                                        <span class="text-sm-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rider->carts->count() > 0)
                                        @foreach($rider->carts as $cart)
                                            <span class="badge badge-{{ $cart->status }}">{{ $cart->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-sm-muted">Belum ditugaskan</span>
                                    @endif
                                </td>
                                <td class="text-sm-muted">{{ $rider->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="flex-gap-2">
                                        <button type="button" @click="$dispatch('open-modal', { type: 'edit', rider: {{ json_encode(['id' => $rider->id, 'name' => $rider->name, 'email' => $rider->email, 'whatsapp' => $rider->whatsapp, 'cart_ids' => $rider->carts->pluck('id')]) }} })" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</button>
                                        <form method="POST" action="{{ route('riders.destroy', $rider) }}" onsubmit="return confirm('Yakin hapus rider ini? Rider akan di-unassign dari semua gerobak.')">
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
                {{ $riders->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Rider</div>
                <div class="empty-state-text">Mulai tambahkan rider untuk mengelola gerobak</div>
                <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Rider</button>
            </div>
        @endif
    </div>
</div>

    <!-- Modal -->
    <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                <div x-show="isOpen" @click.away="closeModal()"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="card max-w-640 text-left transform transition-all" style="width: 100%; max-width: 640px; box-shadow: var(--shadow-lg); text-align: left;">
                    
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title" x-text="type === 'add' ? 'Tambah Rider' : 'Edit Rider'"></h3>
                        <button type="button" @click="closeModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; line-height:1; color: var(--text-muted);">&times;</button>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" :action="formAction">
                            @csrf
                            <template x-if="type === 'edit'">
                                @method('PUT')
                            </template>

                            <div class="form-group">
                                <label class="form-label" for="name">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" class="form-input" x-model="rider.name" placeholder="Nama lengkap rider" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-input" x-model="rider.email" placeholder="rider@example.com" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                                <input type="text" id="whatsapp" name="whatsapp" class="form-input" x-model="rider.whatsapp" placeholder="08xxxxxxxxxx">
                            </div>

                            <template x-if="type === 'edit'">
                                <div class="form-group">
                                    <label class="form-label">Tugaskan Gerobak</label>
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;" x-data="{ allCarts: {{ json_encode($allCarts) }} }">
                                        <template x-for="cart in allCarts.filter(c => c.user_id === null || c.user_id === rider.id)" :key="cart.id">
                                            <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.85rem; border-radius: 0.5rem; border: 1px solid var(--border); cursor: pointer; transition: all 0.2s;">
                                                <input type="checkbox" name="cart_ids[]" :value="cart.id" x-model="rider.cart_ids"
                                                    style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary);">
                                                <div>
                                                    <div class="text-primary-semi" style="display: flex; align-items: center; gap: 4px;">
                                                        <svg class="icon-two-tone" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                                        <span x-text="cart.name"></span>
                                                    </div>
                                                    <div class="text-sm-muted">
                                                        <span :class="'badge badge-' + cart.status" x-text="cart.status"></span>
                                                        <span x-text="cart.user_id === rider.id ? ' Â· Sudah ditugaskan ke rider ini' : ' Â· Belum ada rider'"></span>
                                                    </div>
                                                </div>
                                            </label>
                                        </template>
                                        <div x-show="allCarts.filter(c => c.user_id === null || c.user_id === rider.id).length === 0" class="text-sm-muted">
                                            Semua gerobak sudah ditugaskan ke rider lain.
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label" for="password" x-text="type === 'add' ? 'Password *' : 'Password Baru'"></label>
                                    <input type="password" id="password" name="password" class="form-input" :placeholder="type === 'add' ? 'Minimal 6 karakter' : 'Kosongkan jika tidak diubah'" :required="type === 'add'">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="password_confirmation" x-text="type === 'add' ? 'Konfirmasi Password *' : 'Konfirmasi Password'"></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" :placeholder="type === 'add' ? 'Ulangi password' : 'Ulangi password baru'" :required="type === 'add'">
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                                <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-primary" x-text="type === 'add' ? 'Simpan Rider' : 'Update Rider'"></button>
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
    function riderModal() {
        return {
            isOpen: false,
            type: 'add',
            formAction: '{{ route('riders.store') }}',
            rider: {
                id: '',
                name: '',
                email: '',
                whatsapp: '',
                cart_ids: []
            },
            openModal(detail) {
                this.type = detail.type;
                if (this.type === 'edit') {
                    this.rider = detail.rider;
                    this.formAction = `/riders/${this.rider.id}`;
                } else {
                    this.rider = { id: '', name: '', email: '', whatsapp: '', cart_ids: [] };
                    this.formAction = '{{ route('riders.store') }}';
                }
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
            }
        }
    }
</script>
@endpush
