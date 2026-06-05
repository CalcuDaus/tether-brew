@extends('layouts.app')

@section('title', 'Riwayat Produk Basi')

@section('actions')
    <a href="{{ route('admin.spoiled_products.create') }}" class="btn btn-primary flex-center btn-sm" style="gap:0.5rem;">
        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg> Input Produk Basi Baru
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Produk Basi Harian</h3>
    </div>

    {{-- Date Filter --}}
    <div class="card-body" style="border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
        <form method="GET" action="{{ route('admin.spoiled_products.index') }}"
            style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Filter Tanggal</label>
                <div class="flatpickr-input-container">
                    <input type="text" name="date" id="filter_date" class="form-input datepicker"
                        value="{{ request('date') }}" placeholder="Pilih tanggal...">
                </div>
            </div>
            <div class="form-group" style="display: flex; gap: 8px; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
                <a href="{{ route('admin.spoiled_products.index') }}" class="btn btn-secondary"
                    style="height: 42px; display: flex; align-items: center;">Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body">
        <div class="table-container" style="width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Diinput Oleh</th>
                        <th style="text-align: center;">Total Items Basi</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spoiledProducts as $spoiled)
                        <tr>
                            <td>{{ $spoiled->date->format('d M Y') }}</td>
                            <td>{{ $spoiled->user->name }}</td>
                            <td style="text-align: center; font-weight: 600;">
                                {{ $spoiled->items->sum('quantity') }}
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                    <a href="{{ route('admin.spoiled_products.edit', $spoiled->id) }}"
                                        class="btn btn-outline-primary btn-sm flex-center">
                                        <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Edit
                                    </a>
                                    <form action="{{ route('admin.spoiled_products.destroy', $spoiled->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm flex-center">
                                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                Belum ada data produk basi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($spoiledProducts->hasPages())
            <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $spoiledProducts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#filter_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true,
            disableMobile: "true"
        });
    });
</script>
@endpush
