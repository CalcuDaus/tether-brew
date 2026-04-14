@extends('layouts.app')
@section('title', 'Tambah Gerobak')

@section('actions')
    <a href="{{ route('carts.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg> Form Tambah Gerobak</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('carts.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nama Gerobak *</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="Gerobak Kopi Pak Budi" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-input form-textarea" placeholder="Deskripsi gerobak...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="user_id">Rider</label>
                <select id="user_id" name="user_id" class="form-input form-select">
                    <option value="">-- Pilih Rider --</option>
                    @foreach($riders as $rider)
                        <option value="{{ $rider->id }}" {{ old('user_id') == $rider->id ? 'selected' : '' }}>{{ $rider->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status *</label>
                <select id="status" name="status" class="form-input form-select" required>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="latitude">Latitude</label>
                    <input type="text" id="latitude" name="latitude" class="form-input" value="{{ old('latitude') }}" placeholder="-6.2088">
                </div>
                <div class="form-group">
                    <label class="form-label" for="longitude">Longitude</label>
                    <input type="text" id="longitude" name="longitude" class="form-input" value="{{ old('longitude') }}" placeholder="106.8456">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg> Simpan Gerobak
            </button>
        </form>
    </div>
</div>
@endsection

