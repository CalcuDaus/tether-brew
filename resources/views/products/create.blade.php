@extends('layouts.app')
@section('title', 'Tambah Produk')

@section('actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/></svg> Form Tambah Produk</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nama Produk *</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="Espresso" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-input form-textarea" placeholder="Deskripsi produk...">{{ old('description') }}</textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="price">Harga (Rp) *</label>
                    <input type="number" id="price" name="price" class="form-input" value="{{ old('price') }}" placeholder="15000" min="0" step="500" required>
                    @error('price') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Kategori *</label>
                    <select id="category" name="category" class="form-input form-select" required>
                        <option value="kopi" {{ old('category') == 'kopi' ? 'selected' : '' }}>Kopi</option>
                        <option value="non-kopi" {{ old('category') == 'non-kopi' ? 'selected' : '' }}>Non-Kopi</option>
                        <option value="makanan" {{ old('category') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="form-checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" class="form-checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label form-label-no-margin">Produk Aktif</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">💾 Simpan Produk</button>
        </form>
    </div>
</div>
@endsection

