@extends('layouts.app')

@section('title', 'Kelola Kategori Jurnal')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Kategori Jurnal</h3>
        <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary btn-sm flex-center" style="gap: 0.5rem;">
            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
            </svg> Tambah Kategori
        </button>
    </div>
    <div class="card-body" x-data="categoryModal()" @open-modal.window="openModal($event.detail)">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                        <th style="padding: 12px; font-weight: 600;">Nama Kategori</th>
                        <th style="padding: 12px; font-weight: 600;">Jumlah Jurnal</th>
                        <th style="padding: 12px; font-weight: 600; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='var(--bg-card-hover)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px;">{{ $category->name }}</td>
                        <td style="padding: 12px;">{{ $category->journals_count ?? $category->journals()->count() }}</td>
                        <td style="padding: 12px; text-align: right; display: flex; justify-content: flex-end; gap: 8px;">
                            <button type="button" @click="$dispatch('open-modal', { type: 'edit', category: {{ json_encode(['id' => $category->id, 'name' => $category->name]) }} })" class="btn btn-secondary btn-sm" title="Edit">
                                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <form action="{{ route('admin.journal_categories.destroy', $category->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus kategori ini?" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 24px; text-align: center; color: var(--text-muted);">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                         class="card max-w-640 text-left transform transition-all" style="width: 100%; max-width: 400px; box-shadow: var(--shadow-lg); text-align: left;">
                        
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="card-title" x-text="type === 'add' ? 'Tambah Kategori' : 'Edit Kategori'"></h3>
                            <button type="button" @click="closeModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; line-height:1; color: var(--text-muted);">&times;</button>
                        </div>
                        
                        <div class="card-body">
                            <form method="POST" :action="formAction">
                                @csrf
                                <template x-if="type === 'edit'">
                                    @method('PUT')
                                </template>

                                <div class="form-group mb-4">
                                    <label class="form-label">Nama Kategori</label>
                                    <input type="text" name="name" class="form-input" x-model="category.name" placeholder="Contoh: BAHAN BAKU" required>
                                    @error('name') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                                </div>
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function categoryModal() {
        return {
            isOpen: false,
            type: 'add',
            formAction: '{{ route('admin.journal_categories.store') }}',
            category: {
                id: '',
                name: ''
            },
            openModal(detail) {
                this.type = detail.type;
                if (this.type === 'edit') {
                    this.category = detail.category;
                    this.formAction = `/journal-categories/${this.category.id}`;
                } else {
                    this.category = { id: '', name: '' };
                    this.formAction = '{{ route('admin.journal_categories.store') }}';
                }
                this.isOpen = true;
            },
            closeModal() {
                this.isOpen = false;
            },
            init() {
                @if($errors->any())
                    @if(old('_method') == 'PUT')
                        this.openModal({ type: 'edit', category: { id: '{{ old("id") }}', name: '{{ old("name") }}' } });
                    @else
                        this.openModal({ type: 'add' });
                    @endif
                @endif
            }
        }
    }
</script>
@endpush
