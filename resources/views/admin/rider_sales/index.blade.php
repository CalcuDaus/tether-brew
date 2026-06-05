@extends('layouts.app')

@section('title', 'Daftar Penjualan Rider')

@section('actions')
    <div class="print-hide" style="display: flex; gap: 8px;">
        <button onclick="window.print()" class="btn btn-secondary btn-sm flex-center" style="gap: 0.4rem;">
            <svg width="1.1em" height="1.1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg> Print
        </button>
        <a href="{{ route('admin.rider_sales.create') }}" class="btn btn-primary btn-sm flex-center" style="gap:0.5rem;">
            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
            </svg> Input Penjualan Baru
        </a>
    </div>
@endsection

@section('content')
    <style>
        .action-btn-anim {
            transition: all 0.3s ease !important;
        }
        .action-btn-anim:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.05) !important;
            filter: brightness(1.05);
        }
    </style>
    
    {{-- Filter Card --}}
    <div class="card print-hide" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3 class="card-title">Filter Penjualan</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rider_sales.index') }}" method="GET"
                    style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                    <div class="form-group" style="flex: 1; min-width: 150px; margin: 0;">
                        <label class="form-label"
                            style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Tanggal</label>
                        <input type="text" name="date" id="filter-date" class="form-input" value="{{ $filterDate }}"
                            placeholder="Semua Tanggal" style="height: 40px; border-radius: 8px;">
                    </div>

                    @if(auth()->user()->role === 'bar')
                    <div class="form-group" style="flex: 1.5; min-width: 200px; margin: 0; position: relative;">
                        <label class="form-label"
                            style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Cari Rider</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="text" name="search" class="form-input" value="{{ $search }}"
                                placeholder="Ketik nama rider & tekan Enter..."
                                style="height: 40px; padding-right: 3rem; border-radius: 8px; margin: 0; width: 100%;">
                            <button type="submit"
                                style="position: absolute; right: 0; top: 0; bottom: 0; background: transparent; border: none; padding: 0 15px; color: var(--text-muted); cursor: pointer;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif

                    <div style="display: flex; gap: 8px; margin-bottom: 2px;">
                        <button type="submit" class="btn btn-primary"
                            style="height: 40px; padding: 0 20px; border-radius: 8px; font-weight: 600;">
                            Filter
                        </button>
                        @if($filterDate || $search)
                            <a href="{{ route('admin.rider_sales.index') }}" class="btn btn-secondary"
                                style="height: 40px; padding: 0 20px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            </div>

            <div id="print-area">
                <div class="print-title print-only" style="display: none;">
                    <img src="{{ asset('tether-icon-head.webp') }}" alt="Logo Tether Brew" style="height: 60px; width: auto; display: block; margin: 0 auto 10px auto;">
                    <h2 style="color: #22c55e; font-size: 1.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Tether Brew</h2>
                </div>

                {{-- Summary Cards (only when date is filtered and role is admin) --}}
                @if($summary && $filterDate && auth()->user()->role === 'admin')
                    <div class="summary-cards-container stats-grid mb-4" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; width: 100%;">
                        {{-- Total Cash --}}
                        <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
                            <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #22c55e; z-index: -1;">
                                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
                            </div>
                            <div class="stat-value" style="position: relative; z-index: 2; color: #22c55e;">Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}</div>
                            <div class="stat-label" style="position: relative; z-index: 2;">Total Cash</div>
                        </div>

                        {{-- Total QRIS --}}
                        <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
                            <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #3b82f6; z-index: -1;">
                                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            </div>
                            <div class="stat-value" style="position: relative; z-index: 2; color: #3b82f6;">Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}</div>
                            <div class="stat-label" style="position: relative; z-index: 2;">Total QRIS</div>
                        </div>

                        {{-- Total Minus --}}
                        <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
                            <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #ef4444; z-index: -1;">
                                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
                            </div>
                            <div class="stat-value" style="position: relative; z-index: 2; color: #ef4444;">Rp {{ number_format($summary['total_minus'], 0, ',', '.') }}</div>
                            <div class="stat-label" style="position: relative; z-index: 2;">Total Minus</div>
                        </div>

                        {{-- Jumlah Rider --}}
                        <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
                            <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #8b5cf6; z-index: -1;">
                                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                            <div class="stat-value" style="position: relative; z-index: 2; color: #8b5cf6;">{{ $summary['rider_count'] }} Rider</div>
                            <div class="stat-label" style="position: relative; z-index: 2;">Jumlah Rider</div>
                        </div>

                        {{-- Total Cup --}}
                        <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px; background: var(--gradient-gold); color: white;">
                            <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.25; color: white; z-index: -1;">
                                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                            </div>
                            <div class="stat-value" style="position: relative; z-index: 2; color: white;">{{ number_format($summary['total_cups'], 0, ',', '.') }} Pcs</div>
                            <div class="stat-label" style="position: relative; z-index: 2; opacity: 0.9; color: white;">Total Cup</div>
                        </div>
                    </div>

                    {{-- Confirm to Journal --}}
                    @if($confirmation)
                        <div class="alert alert-success print-hide"
                            style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #16a34a; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                                <span>Data penjualan tanggal <strong>{{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d F Y') }}</strong>
                                    sudah dikonfirmasi ke Jurnal Umum oleh <strong>{{ $confirmation->confirmedByAdmin->name ?? '-' }}</strong>.</span>
                            </div>
                            
                            @if(now()->diffInDays($confirmation->created_at) <= 2)
                                <form id="rollback-journal-form" action="{{ route('admin.rider_sales.rollbackJournal') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $filterDate }}">
                                    <button type="submit" id="btn-rollback-journal" class="btn btn-sm btn-outline-danger flex-center" style="gap: 6px; white-space: nowrap; font-weight: 600; padding: 6px 12px; border-radius: 6px; background: white;">
                                        <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                        Batalkan Konfirmasi
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="print-hide" style="margin-bottom: 20px;">
                            <form id="confirm-journal-form" action="{{ route('admin.rider_sales.confirmJournal') }}" method="POST">
                                @csrf
                                <input type="hidden" name="date" value="{{ $filterDate }}">
                                <button type="submit" class="btn {{ $unverifiedCount > 0 ? 'btn-secondary' : 'btn-primary' }} flex-center"
                                    style="gap: 0.5rem; padding: 10px 24px; border-radius: 10px; font-weight: 600; {{ $unverifiedCount > 0 ? 'opacity: 0.7; cursor: not-allowed;' : '' }}"
                                    {{ $unverifiedCount > 0 ? 'disabled' : '' }}>
                                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                        <polyline points="22 4 12 14.01 9 11.01" />
                                    </svg>
                                    Konfirmasi ke Jurnal Umum
                                </button>
                                @if($unverifiedCount > 0)
                                    <div style="font-size: 0.85rem; color: #ef4444; margin-top: 10px; display: flex; align-items: center; gap: 6px;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        Terdapat <strong>{{ $unverifiedCount }}</strong> penjualan yang belum Anda periksa. Tombol terkunci.
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endif
                @endif

                {{-- Main Data Table --}}
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title">Riwayat Input Penjualan</h3>
                    </div>

                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Rider</th>
                                    @if(auth()->user()->role === 'admin')
                                    <th style="text-align: right;">CASH</th>
                                    <th style="text-align: right;">QRIS</th>
                                    <th style="text-align: right;">Total Setoran</th>
                                    <th style="text-align: right;">TOTAL</th>
                                    @endif
                                    <th style="text-align: right;">Total Cups</th>
                                    <th class="print-hide">Admin Pemeriksa</th>
                                    <th style="text-align: center;" class="print-hide">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->date->format('d M Y') }}</td>
                                        <td style="font-weight: 600;">{{ $sale->rider->name }}</td>
                                        @if(auth()->user()->role === 'admin')
                                        <td style="text-align: right;">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
                                        <td style="text-align: right;">Rp {{ number_format($sale->qris_amount, 0, ',', '.') }}</td>
                                        <td style="text-align: right; font-weight: 600; color: #3b82f6;">Rp
                                            {{ number_format($sale->total_setoran, 0, ',', '.') }}
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: #22c55e;">Rp
                                            {{ number_format($sale->total_gross_income, 0, ',', '.') }}
                                        </td>
                                        @endif
                                        <td style="text-align: right; font-weight: 600; color: #8b5cf6;">
                                            {{ number_format($sale->total_cups ?? 0, 0, ',', '.') }} Pcs
                                        </td>
                                        <td class="print-hide">{{ $sale->admin_pemeriksa ?? $sale->admin->name }}</td>
                                        <td style="text-align: center;" class="print-hide">
                                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                                @if($confirmation && auth()->user()->role === 'admin')
                                                    <button disabled class="btn btn-outline-success btn-sm flex-center action-btn-anim" style="opacity: 0.8; cursor: not-allowed; title="Sudah Dikonfirmasi">
                                                        <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                        </svg>
                                                        Sudah Dikonfirmasi
                                                    </button>
                                                @else
                                                    @php
                                                        $isVerifiedByAdmin = $sale->admin && in_array($sale->admin->role, ['admin', 'owner']);
                                                        
                                                        if (auth()->user()->role === 'admin') {
                                                            if ($isVerifiedByAdmin) {
                                                                $btnClass = "btn-outline-success"; // Outline green
                                                                $btnText = "Diperiksa";
                                                                $icon = '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
                                                            } else {
                                                                $btnClass = "btn-outline-warning"; // Outline orange
                                                                $btnText = "Konfirmasi";
                                                                $icon = '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>';
                                                            }
                                                        } else {
                                                            $btnClass = "btn-outline-primary"; // Outline blue for Bar
                                                            $btnText = "Edit";
                                                            $icon = '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
                                                        }
                                                    @endphp
                                                    <a href="{{ route('admin.rider_sales.edit', $sale->id) }}"
                                                        class="btn {{ $btnClass }} btn-sm flex-center action-btn-anim" title="{{ $btnText }}">
                                                        {!! $icon !!}
                                                        {{ $btnText }}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role === 'admin' ? 9 : 5 }}" style="text-align: center; padding: 30px;">Belum ada riwayat penjualan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($sales->hasPages())
                        <div style="margin-top: 20px;" class="print-hide">
                            {{ $sales->links() }}
                        </div>
                    @endif
        </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            flatpickr("#filter-date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true,
                disableMobile: "true"
            });
        });
    </script>
    @if($summary && $filterDate)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const confirmForm = document.getElementById('confirm-journal-form');
            if (confirmForm) {
                confirmForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Jurnal Umum',
                        html: `
                            <div style="text-align: left; font-family: 'Inter', sans-serif;">
                                <p style="margin-bottom: 16px; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5;">
                                    Apakah Anda yakin ingin mengonfirmasi penjualan rider tanggal <strong>{{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d F Y') }}</strong> ke Jurnal Umum?
                                </p>
                                <div style="background: rgba(0, 0, 0, 0.05); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                                        <span style="color: var(--text-muted); font-weight: 500;">Total Cash:</span>
                                        <span style="color: #4ade80; font-weight: 700;">Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem;">
                                        <span style="color: var(--text-muted); font-weight: 500;">Total QRIS:</span>
                                        <span style="color: #60a5fa; font-weight: 700;">Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                                        <span style="color: var(--text-muted); font-weight: 500;">Total Minus:</span>
                                        <span style="color: #f87171; font-weight: 700;">Rp {{ number_format($summary['total_minus'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0; font-style: italic; line-height: 1.4;">
                                    *Catatan: Nilai minus tidak dicatat ke jurnal sekarang, melainkan akan otomatis tercatat ke Jurnal Umum ketika rider melakukan pembayaran kasbon di masa mendatang.
                                </p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Konfirmasi',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            confirmForm.submit();
                        }
                    });
                });
            }

            const rollbackForm = document.getElementById('rollback-journal-form');
            if (rollbackForm) {
                rollbackForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'PERINGATAN: Batalkan Konfirmasi?',
                        html: `
                            <div style="text-align: left; font-family: 'Inter', sans-serif;">
                                <p style="margin-bottom: 12px; color: #ef4444; font-size: 0.95rem; font-weight: 600;">
                                    Aksi ini akan menghapus riwayat saldo Cash dan QRIS yang telah masuk ke Jurnal Umum!
                                </p>
                                <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5;">
                                    Jika Anda menghapus konfirmasi ini, data pemasukan otomatis akan ditarik kembali. Pastikan laporan buku besar atau laporan bulanan belum final. Apakah Anda yakin ingin membatalkan (rollback)?
                                </p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Batalkan Konfirmasi!',
                        cancelButtonText: 'Kembali',
                        reverseButtons: true,
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            rollbackForm.submit();
                        }
                    });
                });
            }
        });
    </script>
    @endif
@endpush

@push('styles')
    <style>
        .summary-cards-container {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        .summary-cards-container > .card {
            grid-column: span 2;
        }
        .summary-cards-container > .card:nth-child(4),
        .summary-cards-container > .card:nth-child(5) {
            grid-column: span 3;
        }
        @media (max-width: 768px) {
            .summary-cards-container {
                grid-template-columns: repeat(2, 1fr);
            }
            .summary-cards-container > .card,
            .summary-cards-container > .card:nth-child(4),
            .summary-cards-container > .card:nth-child(5) {
                grid-column: span 1;
            }
        }
        @media (max-width: 480px) {
            .summary-cards-container {
                grid-template-columns: 1fr;
            }
            .summary-cards-container > .card,
            .summary-cards-container > .card:nth-child(4),
            .summary-cards-container > .card:nth-child(5) {
                grid-column: span 1;
            }
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }

            .print-hide,
            .print-hide * {
                display: none !important;
                visibility: hidden !important;
            }

            .main-content {
                margin: 0;
                padding: 0;
            }

            .sidebar {
                display: none;
            }

            .topbar {
                display: none;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .summary-cards-container {
                display: grid !important;
                grid-template-columns: repeat(6, 1fr) !important;
                gap: 16px !important;
                margin-bottom: 20px !important;
            }
            .summary-cards-container > .stat-card {
                grid-column: span 2 !important;
                break-inside: avoid;
            }
            .summary-cards-container > .stat-card:nth-child(4),
            .summary-cards-container > .stat-card:nth-child(5) {
                grid-column: span 3 !important;
            }
            .print-title {
                display: block !important;
                text-align: center;
                padding-top: 30px;
                padding-bottom: 20px;
                border-bottom: 2px dashed #cbd5e1;
                margin-bottom: 30px !important;
            }

            .card {
                background: #ffffff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: none !important;
                color: #000000 !important;
                border-radius: 12px !important;
            }
            /* Hide the stat icons in print to prevent clutter */
            .stat-icon-bg {
                display: none !important;
            }
            .stat-card {
                padding: 16px !important;
            }
        }

        /* Card animated and absolute bg icon effects */
        .stat-card-animated {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .stat-card-animated:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        }
        .stat-card-animated .stat-icon-bg {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card-animated:hover .stat-icon-bg {
            transform: scale(1.1) rotate(-5deg);
            opacity: 0.25 !important;
        }
    </style>
@endpush