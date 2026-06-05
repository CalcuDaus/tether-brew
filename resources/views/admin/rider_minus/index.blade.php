@extends('layouts.app')

@section('title', 'Laporan Minus Penjualan')

@section('content')
<div class="stats-grid print-hide mb-4">
    {{-- Card Total Minus --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #ef4444; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="19" x2="12" y2="5"></line>
                <polyline points="5 12 12 5 19 12"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #ef4444;">Rp {{ number_format($totalMinus, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Minus</div>
    </div>
    
    {{-- Card Total Terbayar --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #10b981; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <polyline points="19 12 12 19 5 12"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #10b981;">Rp {{ number_format($minusDibayar, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Terbayar</div>
    </div>

    {{-- Card Total Sisa --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #f59e0b; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="12" y1="12" x2="12" y2="12"></line>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #f59e0b;">Rp {{ number_format($minusBelumDibayar, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Sisa</div>
    </div>
</div>

<div class="card">
    <div class="card-header print-hide" style="padding-bottom: 0;">
        <form method="GET" action="{{ route('admin.rider_minus.index') }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 20px;">
            <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                <label class="form-label">Mulai Tanggal</label>
                <div class="flatpickr-input-container">
                    <input type="text" name="start_date" id="start_date" class="form-input datepicker" value="{{ request('start_date') }}" placeholder="Pilih tanggal...">
                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>
            <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                <label class="form-label">Sampai Tanggal</label>
                <div class="flatpickr-input-container">
                    <input type="text" name="end_date" id="end_date" class="form-input datepicker" value="{{ request('end_date') }}" placeholder="Pilih tanggal...">
                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Rider</label>
                <select name="rider_id" class="form-input">
                    <option value="">Semua Rider</option>
                    @foreach($riders as $rider)
                        <option value="{{ $rider->id }}" {{ request('rider_id') == $rider->id ? 'selected' : '' }}>{{ $rider->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="display: flex; gap: 8px; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
                <a href="{{ route('admin.rider_minus.index') }}" class="btn btn-secondary" style="height: 42px; display: flex; align-items: center;">Reset</a>
            </div>
        </form>
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
                        <th style="text-align: right; color: #10b981;">TERBAYAR</th>
                        <th style="text-align: right; color: #f59e0b;">SISA MINUS</th>
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
                            <td style="text-align: right; color: #10b981;">Rp {{ number_format($sale->minus_paid, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 600; color: #f59e0b;">
                                Rp {{ number_format($sale->minus_amount - $sale->minus_paid, 0, ',', '.') }}<br>
                                @if($sale->minus_status === 'paid')
                                    <span class="badge badge-cash" style="font-size: 0.7rem; padding: 2px 6px;">LUNAS</span>
                                @elseif($sale->minus_status === 'partial')
                                    <span class="badge badge-transfer" style="font-size: 0.7rem; padding: 2px 6px;">SEBAGIAN</span>
                                @else
                                    <span class="badge badge-qris" style="font-size: 0.7rem; padding: 2px 6px;">BELUM LUNAS</span>
                                @endif
                            </td>
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
                            <td colspan="9" style="text-align: center; padding: 30px;">Belum ada riwayat minus penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
