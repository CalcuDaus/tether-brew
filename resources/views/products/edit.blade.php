@extends('layouts.app')
@section('title', 'Edit Produk')

@section('actions')
    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Produk: {{ $product->name }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Nama Produk *</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $product->name) }}" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-input form-textarea">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="price">Harga (Rp) *</label>
                    <input type="number" id="price" name="price" class="form-input" value="{{ old('price', $product->price) }}" min="0" step="500" required>
                    @error('price') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Kategori *</label>
                    <select id="category" name="category" class="form-input form-select" required>
                        <option value="kopi" {{ old('category', $product->category) == 'kopi' ? 'selected' : '' }}>Kopi</option>
                        <option value="non-kopi" {{ old('category', $product->category) == 'non-kopi' ? 'selected' : '' }}>Non-Kopi</option>
                        <option value="makanan" {{ old('category', $product->category) == 'makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="lainnya" {{ old('category', $product->category) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="form-checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" class="form-checkbox" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label form-label-no-margin">Produk Aktif</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">💾 Update Produk</button>
        </form>
    </div>
</div>
@endsection

