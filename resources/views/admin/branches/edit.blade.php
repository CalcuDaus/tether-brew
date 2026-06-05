@extends('layouts.app')
@section('title', 'Edit Cabang')

@section('actions')
    <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title">
            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> 
            Form Edit Cabang
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('branches.update', $branch->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid-2">
                <div class="form-group">
                    <label for="name" class="form-label">Nama Cabang *</label>
                    <input type="text" class="form-input" id="name" name="name" value="{{ old('name', $branch->name) }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="code" class="form-label">Kode (URL Slug) *</label>
                    <input type="text" class="form-input" id="code" name="code" value="{{ old('code', $branch->code) }}" required>
                    <div class="text-xs-muted mt-1" style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 4px;">Unik, huruf kecil tanpa spasi.</div>
                    @error('code') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">No. Telepon / WhatsApp</label>
                <input type="text" class="form-input" id="phone" name="phone" value="{{ old('phone', $branch->phone) }}">
                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-input" id="address" name="address" rows="3">{{ old('address', $branch->address) }}</textarea>
                @error('address') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 1rem;">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                <label for="is_active" class="form-label" style="margin: 0; cursor: pointer;">Cabang Aktif</label>
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> 
                    Update Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
