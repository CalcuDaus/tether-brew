@extends('layouts.app')
@section('title', 'Edit Rider')

@section('actions')
    <a href="{{ route('riders.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Rider: {{ $rider->name }}</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('riders.update', $rider) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap *</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $rider->name) }}" placeholder="Nama lengkap rider" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $rider->email) }}" placeholder="rider@example.com" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                <input type="text" id="whatsapp" name="whatsapp" class="form-input" value="{{ old('whatsapp', $rider->whatsapp) }}" placeholder="08xxxxxxxxxx">
                @error('whatsapp') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tugaskan Gerobak</label>
                @if($availableCarts->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($availableCarts as $cart)
                            <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.85rem; border-radius: 0.5rem; border: 1px solid var(--border); cursor: pointer; transition: all 0.2s;">
                                <input type="checkbox" name="cart_ids[]" value="{{ $cart->id }}"
                                    {{ in_array($cart->id, old('cart_ids', $rider->carts->pluck('id')->toArray())) ? 'checked' : '' }}
                                    style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary);">
                                <div>
                                    <div class="text-primary-semi">
                                        <svg class="icon-two-tone" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                        {{ $cart->name }}
                                    </div>
                                    <div class="text-sm-muted">
                                        <span class="badge badge-{{ $cart->status }}">{{ ucfirst($cart->status) }}</span>
                                        @if($cart->user_id === $rider->id)
                                            · Sudah ditugaskan ke rider ini
                                        @else
                                            · Belum ada rider
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm-muted">Semua gerobak sudah ditugaskan ke rider lain.</div>
                @endif
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="password">Password Baru</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Kosongkan jika tidak diubah">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi password baru">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg> Perbarui Rider
            </button>
        </form>
    </div>
</div>
@endsection
