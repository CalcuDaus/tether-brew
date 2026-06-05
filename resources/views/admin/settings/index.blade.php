@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pengaturan Sistem</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            
            <div style="max-width: 600px;">
                @foreach($settings as $setting)
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">{{ $setting->label ?? $setting->key }}</label>
                    <input type="text" name="settings[{{ $setting->key }}]" class="form-input" value="{{ $setting->value }}" required>
                </div>
                @endforeach
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary flex-center" style="gap: 0.5rem;">
                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline>
                    </svg> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
