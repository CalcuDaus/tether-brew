@extends('layouts.app')
@section('title', 'Kelola Produk')

@section('actions')
    <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Produk</button>
@endsection

@section('content')
<div x-data="productModal()" @open-modal.window="openModal($event.detail)">
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($products->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>#{{ $product->id }}</td>
                                <td>
                                    <div class="text-primary-semi">
                                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                                        </svg> {{ $product->name }}
                                    </div>
                                    @if($product->description)
                                        <div class="text-sm-muted mt-2-custom">{{ Str::limit($product->description, 50) }}</div>
                                    @endif
                                </td>
                                <td><span class="badge badge-category">{{ $product->category }}</span></td>
                                <td class="text-gold-semi">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge badge-active">Aktif</span>
                                    @else
                                        <span class="badge badge-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex-gap-2">
                                        <button type="button" @click="$dispatch('open-modal', { type: 'edit', product: {{ json_encode(['id' => $product->id, 'name' => $product->name, 'description' => $product->description, 'category' => $product->category, 'price' => $product->price, 'is_active' => $product->is_active]) }} })" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</button>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Yakin hapus produk ini?')">
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
                {{ $products->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Produk</div>
                <div class="empty-state-text">Mulai tambahkan produk kopi Anda</div>
                <button type="button" @click="$dispatch('open-modal', { type: 'add' })" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Produk</button>
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
                        <h3 class="card-title" x-text="type === 'add' ? 'Tambah Produk' : 'Edit Produk'"></h3>
                        <button type="button" @click="closeModal()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; line-height:1; color: var(--text-muted);">&times;</button>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" :action="formAction">
                            @csrf
                            <template x-if="type === 'edit'">
                                @method('PUT')
                            </template>

                            <div class="form-group">
                                <label class="form-label" for="name">Nama Produk *</label>
                                <input type="text" id="name" name="name" class="form-input" x-model="product.name" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="category">Kategori *</label>
                                <input type="text" id="category" name="category" class="form-input" x-model="product.category" placeholder="Contoh: Coffee, Non-Coffee" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="price">Harga (Rp) *</label>
                                <input type="number" id="price" name="price" class="form-input" x-model="product.price" min="0" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="description">Deskripsi</label>
                                <textarea id="description" name="description" class="form-input form-textarea" x-model="product.description" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                    <input type="checkbox" name="is_active" value="1" x-model="product.is_active" style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary);">
                                    <span style="font-weight: 500;">Produk Aktif (Tersedia)</span>
                                </label>
                                <div class="text-sm-muted" style="margin-top: 4px; margin-left: 1.6rem;">Hapus centang jika produk sedang tidak dijual.</div>
                            </div>

                            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                                <button type="button" @click="closeModal()" class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-primary" x-text="type === 'add' ? 'Simpan Produk' : 'Update Produk'"></button>
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
    function productModal() {
        return {
            isOpen: false,
            type: 'add',
            formAction: '{{ route('products.store') }}',
            product: {
                id: '',
                name: '',
                description: '',
                category: '',
                price: '',
                is_active: true
            },
            openModal(detail) {
                this.type = detail.type;
                if (this.type === 'edit') {
                    this.product = detail.product;
                    this.product.is_active = detail.product.is_active == 1;
                    this.formAction = `/products/${this.product.id}`;
                } else {
                    this.product = { id: '', name: '', description: '', category: '', price: '', is_active: true };
                    this.formAction = '{{ route('products.store') }}';
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

