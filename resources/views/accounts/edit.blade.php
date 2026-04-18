@extends('layouts.app')
@section('title', 'Edit Akun')

@section('actions')
    <a href="{{ route('accounts.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="card max-w-640">
    <div class="card-header">
        <h3 class="card-title"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Form Edit Akun</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('accounts.update', $account) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap *</label>
                <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $account->name) }}" placeholder="Nama lengkap" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $account->email) }}" placeholder="email@example.com" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                <input type="text" id="whatsapp" name="whatsapp" class="form-input" value="{{ old('whatsapp', $account->whatsapp) }}" placeholder="08xxxxxxxxxx">
                @error('whatsapp') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="role">Pilih Role *</label>
                <select id="role" name="role" class="form-input" required {{ auth()->id() === $account->id ? 'disabled' : '' }}>
                    <option value="" disabled>— Pilih Role —</option>
                    <option value="owner" {{ old('role', $account->role) === 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="admin" {{ old('role', $account->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="rider" {{ old('role', $account->role) === 'rider' ? 'selected' : '' }}>Rider</option>
                </select>
                @if(auth()->id() === $account->id)
                    <input type="hidden" name="role" value="{{ $account->role }}">
                    <div class="text-xs-muted mt-1">Anda tidak dapat mengubah role akun Anda sendiri saat sedang login.</div>
                @endif
                @error('role') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <div class="flex-gap-2" style="justify-content:space-between;">
                    <label class="form-label" for="password">Ganti Password</label>
                    <span class="text-xs-muted">Kosongkan jika tidak ingin mengubah</span>
                </div>
                <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 6 karakter">
                @error('password') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="btn btn-primary">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
