@extends('layouts.app')
@section('title', 'Tambah Akun')

@section('actions')
    <a href="{{ route('accounts.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" x2="20" y1="8" y2="14"/><line x1="23" x2="17" y1="11" y2="11"/></svg> Form Tambah Akun</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('accounts.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap *</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="Nama lengkap" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="email@example.com" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                <input type="text" id="whatsapp" name="whatsapp" class="form-input" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx">
                @error('whatsapp') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Pilih Role *</label>
                <select id="role" name="role" class="form-input" required>
                    <option value="" disabled selected>— Pilih Role —</option>
                    <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="rider" {{ old('role') === 'rider' ? 'selected' : '' }}>Rider</option>
                </select>
                @error('role') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 6 karakter" required>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg> Simpan Akun
            </button>
        </form>
    </div>
</div>
@endsection
