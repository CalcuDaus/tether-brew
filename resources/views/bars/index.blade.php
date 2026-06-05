@extends('layouts.app')
@section('title', 'Kelola Bar')

@section('actions')
    <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary btn-sm flex-center" style="gap: 0.5rem;">
        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Akun Bar
    </button>
@endsection

@section('content')
<div x-data="barModal()" @open-modal.window="openModal($event.detail)">
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($bars->count() > 0)
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Bar</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Bergabung</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bars as $bar)
                            <tr>
                                <td class="text-primary-semi">{{ $bar->name }}</td>
                                <td>{{ $bar->email }}</td>
                                <td>{{ $bar->whatsapp ?? '-' }}</td>
                                <td>{{ $bar->created_at->format('d M Y') }}</td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                        <button type="button" @click="$dispatch('open-modal', { type: 'edit', bar: {{ json_encode(['id' => $bar->id, 'name' => $bar->name, 'email' => $bar->email, 'whatsapp' => $bar->whatsapp]) }} })" class="btn btn-outline-primary btn-sm flex-center">
                                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Edit
                                        </button>
                                        <form method="POST" action="{{ route('bars.destroy', $bar) }}" data-confirm="Yakin hapus akun bar ini?">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm flex-center">
                                                <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4-custom">
                {{ $bars->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Akun Bar</div>
                <div class="empty-state-text">Mulai tambahkan akun bar untuk cabang ini</div>
                <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Akun Bar</button>
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
                        <h3 class="card-title" x-text="type === 'add' ? 'Form Tambah Akun Bar' : 'Edit Akun Bar'"></h3>
                        <button type="button" @click="closeModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; line-height:1; color: var(--text-muted);">&times;</button>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" :action="formAction">
                            @csrf
                            <template x-if="type === 'edit'">
                                @method('PUT')
                            </template>

                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label class="form-label" for="name">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" class="form-input" x-model="bar.name" required>
                            </div>

                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label class="form-label" for="email">Email Login *</label>
                                <input type="email" id="email" name="email" class="form-input" x-model="bar.email" required>
                            </div>

                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label class="form-label" for="whatsapp">No. WhatsApp</label>
                                <input type="text" id="whatsapp" name="whatsapp" class="form-input" x-model="bar.whatsapp" placeholder="Contoh: 081234567890">
                            </div>

                            <div style="display: flex; gap: 15px; margin-bottom: 1.5rem;">
                                <div class="form-group" style="flex: 1; margin: 0;">
                                    <label class="form-label" for="password" x-text="type === 'add' ? 'Password *' : 'Password Baru'"></label>
                                    <input type="password" id="password" name="password" class="form-input" :required="type === 'add'">
                                </div>
                                <div class="form-group" style="flex: 1; margin: 0;">
                                    <label class="form-label" for="password_confirmation" x-text="type === 'add' ? 'Konfirmasi Password *' : 'Konfirmasi Password'"></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" :required="type === 'add'">
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-primary" x-text="type === 'add' ? 'Simpan Akun Bar' : 'Update Akun Bar'"></button>
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
    function barModal() {
        return {
            isOpen: false,
            type: 'add',
            formAction: '{{ route('bars.store') }}',
            bar: {
                id: '',
                name: '',
                email: '',
                whatsapp: ''
            },
            openModal(detail) {
                this.type = detail.type;
                if (this.type === 'edit') {
                    this.bar = detail.bar;
                    this.formAction = `/bars/${this.bar.id}`;
                } else {
                    this.bar = { id: '', name: '', email: '', whatsapp: '' };
                    this.formAction = '{{ route('bars.store') }}';
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
