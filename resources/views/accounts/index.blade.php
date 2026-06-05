@extends('layouts.app')
@section('title', 'Kelola Akun')

@section('actions')
    <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Akun</button>
@endsection

@section('content')
<div x-data="accountModal()" @open-modal.window="openModal($event.detail)">
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($accounts->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Role</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td>#{{ $account->id }}</td>
                                <td class="text-primary-semi">
                                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg> {{ $account->name }}
                                </td>
                                <td>{{ $account->email }}</td>
                                <td>
                                    @if($account->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $account->whatsapp) }}" target="_blank" class="text-xs-green">
                                            {{ $account->whatsapp }}
                                        </a>
                                    @else
                                        <span class="text-sm-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $account->role === 'owner' ? 'active' : ($account->role === 'admin' ? 'draft' : 'inactive') }}">
                                        {{ ucfirst($account->role) }}
                                    </span>
                                </td>
                                <td class="text-sm-muted">{{ $account->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="flex-gap-2">
                                        <button type="button" @click="$dispatch('open-modal', { type: 'edit', account: {{ json_encode(['id' => $account->id, 'name' => $account->name, 'email' => $account->email, 'whatsapp' => $account->whatsapp, 'role' => $account->role, 'branch_id' => $account->branch_id]) }} })" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</button>
                                        @if(auth()->id() !== $account->id)
                                        <form method="POST" action="{{ route('accounts.destroy', $account) }}" onsubmit="return confirm('Yakin hapus akun ini? Menghapus rider akan memutus tugasnya dari gerobak.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4-custom">
                {{ $accounts->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Akun</div>
                <div class="empty-state-text">Mulai tambahkan akun untuk mengelola pengguna</div>
                <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Akun</button>
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
                        <h3 class="card-title" x-text="type === 'add' ? 'Tambah Akun' : 'Edit Akun'"></h3>
                        <button type="button" @click="closeModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; line-height:1; color: var(--text-muted);">&times;</button>
                    </div>
                    
                    <div class="card-body">
                        <!-- Validation Errors (handled globally usually, but form action handles redirect back) -->
                        <form method="POST" :action="formAction">
                            @csrf
                            <template x-if="type === 'edit'">
                                @method('PUT')
                            </template>

                            <div class="form-group">
                                <label class="form-label" for="name">Nama Lengkap *</label>
                                <input type="text" id="name" name="name" class="form-input" x-model="account.name" placeholder="Nama lengkap" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-input" x-model="account.email" placeholder="email@example.com" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                                <input type="text" id="whatsapp" name="whatsapp" class="form-input" x-model="account.whatsapp" placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="role">Pilih Role *</label>
                                <select id="role" name="role" class="form-input" x-model="account.role" required>
                                    <option value="" disabled>— Pilih Role —</option>
                                    <option value="owner">Owner</option>
                                    <option value="admin">Admin</option>
                                    <option value="rider">Rider</option>
                                    <option value="bar">Bar</option>
                                </select>
                            </div>

                            <div class="form-group" x-show="account.role === 'admin' || account.role === 'rider' || account.role === 'bar'">
                                <label class="form-label" for="branch_id">Pilih Cabang (Khusus Admin, Rider, Bar)</label>
                                <select id="branch_id" name="branch_id" class="form-input" x-model="account.branch_id">
                                    <option value="">— Pilih Cabang —</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-xs-muted mt-1" style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 4px;">Catatan: Owner tidak di-scope, role lain akan di-scope ke cabang yang dipilih.</div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label" for="password">Password <span x-show="type === 'add'">*</span><span x-show="type === 'edit'" style="font-weight:normal; font-size:0.8rem">(Opsional)</span></label>
                                    <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 6 karakter" x-bind:required="type === 'add'">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="password_confirmation">Konfirmasi Password <span x-show="type === 'add'">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi password" x-bind:required="type === 'add'">
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                                <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-primary" x-text="type === 'add' ? 'Simpan Akun' : 'Update Akun'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function accountModal() {
        return {
            isOpen: false,
            type: 'add',
            formAction: '{{ route('accounts.store') }}',
            account: {
                id: '',
                name: '',
                email: '',
                whatsapp: '',
                role: '',
                branch_id: ''
            },
            openModal(detail) {
                this.type = detail.type;
                if (this.type === 'edit') {
                    this.account = detail.account;
                    this.formAction = `/accounts/${this.account.id}`;
                } else {
                    this.account = { id: '', name: '', email: '', whatsapp: '', role: '', branch_id: '' };
                    this.formAction = '{{ route('accounts.store') }}';
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
@endsection
