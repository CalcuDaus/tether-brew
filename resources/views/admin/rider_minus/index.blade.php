@extends('layouts.app')

@section('title', 'Laporan Minus Penjualan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Minus Penjualan</h3>
    </div>
    
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Rider</th>
                        <th style="text-align: right;">CASH</th>
                        <th style="text-align: right;">Actual Setor</th>
                        <th style="text-align: right; color: #ef4444;">MINUS</th>
                        <th>Admin Pemeriksa</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->date->format('d M Y') }}</td>
                            <td style="font-weight: 600;">{{ $sale->rider->name }}</td>
                            <td style="text-align: right;">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($sale->actual_setor, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 700; color: #ef4444;">Rp {{ number_format($sale->minus_amount, 0, ',', '.') }}</td>
                            <td>{{ $sale->admin_pemeriksa ?? $sale->admin->name }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.rider_sales.edit', $sale->id) }}" class="btn btn-sm btn-outline-primary" style="padding: 4px 8px; font-size: 0.85rem;">
                                    <svg class="icon-two-tone" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg> Edit Penjualan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px;">Belum ada riwayat minus penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
