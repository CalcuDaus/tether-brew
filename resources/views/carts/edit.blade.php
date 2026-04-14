@extends('layouts.app')
@section('title', 'Edit Gerobak')

@section('actions')
    <a href="{{ route('carts.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Gerobak: {{ $cart->name }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('carts.update', $cart) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Nama Gerobak *</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $cart->name) }}" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-input form-textarea">{{ old('description', $cart->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="user_id">Rider</label>
                <select id="user_id" name="user_id" class="form-input form-select">
                    <option value="">-- Pilih Rider --</option>
                    @foreach($riders as $rider)
                        <option value="{{ $rider->id }}" {{ old('user_id', $cart->user_id) == $rider->id ? 'selected' : '' }}>{{ $rider->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status *</label>
                <select id="status" name="status" class="form-input form-select" required>
                    <option value="inactive" {{ old('status', $cart->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="active" {{ old('status', $cart->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ old('status', $cart->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="latitude">Latitude</label>
                    <input type="text" id="latitude" name="latitude" class="form-input" value="{{ old('latitude', $cart->location?->latitude) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="longitude">Longitude</label>
                    <input type="text" id="longitude" name="longitude" class="form-input" value="{{ old('longitude', $cart->location?->longitude) }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">💾 Update Gerobak</button>
        </form>
    </div>
</div>
@endsection

