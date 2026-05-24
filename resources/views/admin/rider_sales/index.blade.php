@extends('layouts.app')

@section('title', 'Daftar Penjualan Rider')

@section('actions')
    <a href="{{ route('admin.rider_sales.create') }}" class="btn btn-primary btn-sm flex-center" style="gap:0.5rem;">
        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
        </svg> Input Penjualan Baru
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Input Penjualan</h3>
    </div>
    
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Rider</th>
                        <th style="text-align: right;">CASH</th>
                        <th style="text-align: right;">QRIS</th>
                        <th style="text-align: right;">Total Setoran</th>
                        <th style="text-align: right;">TOTAL</th>
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
                            <td style="text-align: right;">Rp {{ number_format($sale->qris_amount, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 600; color: #3b82f6;">Rp {{ number_format($sale->total_setoran, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 700; color: #22c55e;">Rp {{ number_format($sale->total_gross_income, 0, ',', '.') }}</td>
                            <td>{{ $sale->admin_pemeriksa ?? $sale->admin->name }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.rider_sales.edit', $sale->id) }}" class="btn btn-sm btn-outline-primary" style="padding: 4px 8px; font-size: 0.85rem;">
                                    <svg class="icon-two-tone" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px;">Belum ada riwayat penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
