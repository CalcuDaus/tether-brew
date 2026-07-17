@extends('layouts.app')

@section('title', 'Laporan Omset')

@section('actions')
    <div class="print-hide" style="display: flex; gap: 8px;">
        <button onclick="window.print()" class="btn btn-secondary btn-sm flex-center" style="gap: 0.4rem;">
            <svg width="1.1em" height="1.1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg> Print
        </button>
        <a href="{{ route('admin.revenue_report.export_excel', request()->query()) }}" class="btn btn-sm flex-center" style="gap: 0.4rem; background: #16a34a; color: white; border: none;">
            <svg width="1.1em" height="1.1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg> Export Excel
        </a>
    </div>
@endsection

@section('content')
    <style>
        .filter-type-btn {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1.5px solid var(--border-color);
            background: white;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .filter-type-btn:hover {
            border-color: #22c55e;
            color: #22c55e;
        }
        .filter-type-btn.active {
            background: #22c55e;
            border-color: #22c55e;
            color: white;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-badge.confirmed {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .status-badge.unconfirmed {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
    </style>

    {{-- Filter Card --}}
    <div class="card print-hide" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan Omset</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.revenue_report.index') }}" method="GET" id="filter-form">
                {{-- Filter Type Buttons --}}
                <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                    <button type="button" class="filter-type-btn {{ $filterType === 'daily' ? 'active' : '' }}" data-type="daily">Harian</button>
                    <button type="button" class="filter-type-btn {{ $filterType === 'range' ? 'active' : '' }}" data-type="range">Range Tanggal</button>
                    <button type="button" class="filter-type-btn {{ $filterType === 'monthly' ? 'active' : '' }}" data-type="monthly">Bulanan</button>
                </div>

                <input type="hidden" name="filter_type" id="filter-type-input" value="{{ $filterType }}">

                <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                    {{-- Daily Filter --}}
                    <div class="form-group filter-daily {{ $filterType === 'daily' ? '' : 'hidden' }}" style="flex: 1; min-width: 180px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Tanggal</label>
                        <input type="text" name="date" id="filter-date" class="form-input" value="{{ $filterDate }}" placeholder="Pilih Tanggal" style="height: 40px; border-radius: 8px;">
                    </div>

                    {{-- Range Filter --}}
                    <div class="form-group filter-range {{ $filterType === 'range' ? '' : 'hidden' }}" style="flex: 1; min-width: 150px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Dari Tanggal</label>
                        <input type="text" name="date_from" id="filter-date-from" class="form-input" value="{{ $dateFrom }}" placeholder="Tanggal Awal" style="height: 40px; border-radius: 8px;">
                    </div>
                    <div class="form-group filter-range {{ $filterType === 'range' ? '' : 'hidden' }}" style="flex: 1; min-width: 150px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Sampai Tanggal</label>
                        <input type="text" name="date_to" id="filter-date-to" class="form-input" value="{{ $dateTo }}" placeholder="Tanggal Akhir" style="height: 40px; border-radius: 8px;">
                    </div>

                    {{-- Monthly Filter --}}
                    <div class="form-group filter-monthly {{ $filterType === 'monthly' ? '' : 'hidden' }}" style="flex: 1; min-width: 130px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Bulan</label>
                        <select name="month" class="form-input" style="height: 40px; border-radius: 8px;">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group filter-monthly {{ $filterType === 'monthly' ? '' : 'hidden' }}" style="flex: 0.5; min-width: 100px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Tahun</label>
                        <select name="year" class="form-input" style="height: 40px; border-radius: 8px;">
                            @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="form-group" style="flex: 1; min-width: 160px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; font-size: 0.8rem; margin-bottom: 6px; text-transform: uppercase; color: var(--text-muted);">Status Konfirmasi</label>
                        <select name="status" class="form-input" style="height: 40px; border-radius: 8px;">
                            <option value="">Semua</option>
                            <option value="confirmed" {{ $confirmationStatus === 'confirmed' ? 'selected' : '' }}>Sudah Dikonfirmasi</option>
                            <option value="unconfirmed" {{ $confirmationStatus === 'unconfirmed' ? 'selected' : '' }}>Belum Dikonfirmasi</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div style="display: flex; gap: 8px; margin-bottom: 2px;">
                        <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 20px; border-radius: 8px; font-weight: 600;">
                            Filter
                        </button>
                        <a href="{{ route('admin.revenue_report.index') }}" class="btn btn-secondary" style="height: 40px; padding: 0 20px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="print-area">
        {{-- Print Header --}}
        <div class="print-title print-only" style="display: none;">
            <img src="{{ asset('tether-icon-head.webp') }}" alt="Logo Tether Brew" style="height: 60px; width: auto; display: block; margin: 0 auto 10px auto;">
            <h2 style="color: #22c55e; font-size: 1.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Tether Brew</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-top: 5px;">Laporan Omset — {{ $periodLabel }}</p>
        </div>

        {{-- Summary Cards --}}
        @if($summary)
            <div class="summary-cards-container stats-grid mb-4" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; width: 100%;">
                {{-- Total Omset --}}
                <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px; background: var(--gradient-gold); color: white;">
                    <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.25; color: white; z-index: -1;">
                        <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </div>
                    <div class="stat-value" style="position: relative; z-index: 2; color: white;">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</div>
                    <div class="stat-label" style="position: relative; z-index: 2; opacity: 0.9; color: white;">Total Omset</div>
                </div>

                {{-- Setoran Cash (Fisik) --}}
                <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
                    <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #22c55e; z-index: -1;">
                        <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
                    </div>
                    <div class="stat-value" style="position: relative; z-index: 2; color: #22c55e;">Rp {{ number_format($summary['total_actual_setor'], 0, ',', '.') }}</div>
                    <div class="stat-label" style="position: relative; z-index: 2;">Setoran Cash (Fisik)</div>
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

                {{-- Total Cup --}}
                <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
                    <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #8b5cf6; z-index: -1;">
                        <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                    </div>
                    <div class="stat-value" style="position: relative; z-index: 2; color: #8b5cf6;">{{ number_format($summary['total_cups'], 0, ',', '.') }} Pcs</div>
                    <div class="stat-label" style="position: relative; z-index: 2;">Total Cup</div>
                </div>
            </div>

            {{-- Extra Info Row --}}
            <div style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
                <div style="background: rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.2); border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span style="font-weight: 600; color: #8b5cf6; font-size: 0.9rem;">{{ $summary['rider_count'] }} Rider</span>
                </div>
                <div style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 10px; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span style="font-weight: 600; color: #22c55e; font-size: 0.9rem;">Periode: {{ $periodLabel }}</span>
                </div>
            </div>
        @endif

        {{-- Main Data Table --}}
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Data Penjualan</h3>
                @if($summary)
                    <span style="font-size: 0.85rem; color: var(--text-muted);">
                        {{ $sales instanceof \Illuminate\Pagination\LengthAwarePaginator ? $sales->total() : $sales->count() }} data
                    </span>
                @endif
            </div>

            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-transform: uppercase;">Tanggal</th>
                                <th style="text-transform: uppercase;">Rider</th>
                                <th style="text-align: right; text-transform: uppercase;">Cash</th>
                                <th style="text-align: right; text-transform: uppercase;">QRIS</th>
                                <th style="text-align: right; text-transform: uppercase;">Setoran Fisik</th>
                                <th style="text-align: right; text-transform: uppercase;" class="print-hide-col">Total Setoran</th>
                                <th style="text-align: right; text-transform: uppercase;">Total</th>
                                <th style="text-align: right; text-transform: uppercase;">Total Cups</th>
                                <th style="text-transform: uppercase;" class="print-hide-col">Admin Pemeriksa</th>
                                <th style="text-align: center; text-transform: uppercase;" class="print-hide-col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                @php
                                    $dateStr = $sale->date->format('Y-m-d');
                                    $isConfirmed = isset($confirmedDatesMap[$dateStr]);
                                @endphp
                                <tr>
                                    <td>{{ $sale->date->format('d M Y') }}</td>
                                    <td style="font-weight: 600;">{{ $sale->rider->name }}</td>
                                    <td style="text-align: right; color: var(--text-muted);">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
                                    <td style="text-align: right;">Rp {{ number_format($sale->qris_amount, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 600; color: #22c55e;">Rp {{ number_format($sale->actual_setor, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 600; color: #3b82f6;" class="print-hide-col">Rp {{ number_format($sale->total_setoran, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 700; color: #22c55e;">Rp {{ number_format($sale->total_gross_income, 0, ',', '.') }}</td>
                                    <td style="text-align: right; font-weight: 600; color: #8b5cf6;">{{ number_format($sale->total_cups ?? 0, 0, ',', '.') }} Pcs</td>
                                    <td class="print-hide-col">{{ $sale->admin_pemeriksa ?? ($sale->admin->name ?? '-') }}</td>
                                    <td style="text-align: center;" class="print-hide-col">
                                        @if($isConfirmed)
                                            <span class="status-badge confirmed">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                Dikonfirmasi
                                            </span>
                                        @else
                                            <span class="status-badge unconfirmed">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 10px; opacity: 0.4;">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        <div style="font-weight: 600; font-size: 1rem; margin-bottom: 4px;">Belum ada data</div>
                                        <div style="font-size: 0.85rem;">Gunakan filter di atas untuk menampilkan laporan omset.</div>
                                    </td>
                                </tr>
                            @endforelse

                            {{-- Grand Total Row --}}
                            @if($summary && $sales->count() > 0)
                                <tr style="background: rgba(34, 197, 94, 0.06); font-weight: 700; border-top: 2px solid #22c55e;">
                                    <td colspan="2" style="font-weight: 800; color: #1e293b;">GRAND TOTAL</td>
                                    <td style="text-align: right; color: var(--text-muted);">Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}</td>
                                    <td style="text-align: right;">Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: #22c55e;">Rp {{ number_format($summary['total_actual_setor'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: #3b82f6;" class="print-hide-col">Rp {{ number_format($summary['total_actual_setor'] + $summary['total_qris'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: #22c55e;">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</td>
                                    <td style="text-align: right; color: #8b5cf6;">{{ number_format($summary['total_cups'], 0, ',', '.') }} Pcs</td>
                                    <td colspan="2" class="print-hide-col"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if($sales instanceof \Illuminate\Pagination\LengthAwarePaginator && $sales->hasPages())
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
            // Date pickers
            flatpickr("#filter-date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true,
                disableMobile: "true"
            });
            flatpickr("#filter-date-from", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true,
                disableMobile: "true"
            });
            flatpickr("#filter-date-to", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true,
                disableMobile: "true"
            });

            // Filter type toggle
            const filterTypeBtns = document.querySelectorAll('.filter-type-btn');
            const filterTypeInput = document.getElementById('filter-type-input');

            filterTypeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const type = btn.dataset.type;
                    filterTypeInput.value = type;

                    filterTypeBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Show/hide relevant filter fields
                    document.querySelectorAll('.filter-daily').forEach(el => el.classList.toggle('hidden', type !== 'daily'));
                    document.querySelectorAll('.filter-range').forEach(el => el.classList.toggle('hidden', type !== 'range'));
                    document.querySelectorAll('.filter-monthly').forEach(el => el.classList.toggle('hidden', type !== 'monthly'));
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .hidden {
            display: none !important;
        }

        .summary-cards-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        @media (max-width: 1024px) {
            .summary-cards-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .summary-cards-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .summary-cards-container {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .table th, .table td {
                padding: 8px !important;
                font-size: 0.85rem !important;
            }

            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area {
                position: absolute;
                left: 0; top: 0;
                width: 100%;
                box-shadow: none !important;
                border: none !important;
            }
            .print-hide, .print-hide * { display: none !important; visibility: hidden !important; }
            .print-hide-col { display: none !important; }
            .main-content { margin: 0; padding: 0; }
            .sidebar { display: none; }
            .topbar { display: none; }
            body { background: white !important; color: black !important; }

            .summary-cards-container {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 15px !important;
            }
            .stat-icon-bg { display: none !important; }
            .stat-card { padding: 12px !important; }

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
        }

        /* Card animated effects */
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
