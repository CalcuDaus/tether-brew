@extends('layouts.app')

@section('title', 'Payroll Rider')

@section('content')
    <div class="card mb-4 print-hide" style="margin-bottom: 8px;">
        <div class="card-header">
            <h3 class="card-title">Filter Payroll</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payroll.index') }}" id="filter-form"
                style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">

                <div class="form-group" style="flex: 1; min-width: 160px; margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Tipe Gaji
                    </label>
                    <select name="filter_type" id="filter_type" class="form-input payroll-select" onchange="toggleFilterFields()">
                        <option value="weekly" {{ $filterType === 'weekly' ? 'selected' : '' }}>Gaji Mingguan</option>
                        <option value="custom" {{ $filterType === 'custom' ? 'selected' : '' }}>Custom Range</option>
                        <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Gaji Akhir Bulan</option>
                    </select>
                </div>

                <div class="form-group" style="flex: 1; min-width: 180px; margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Rider
                    </label>
                    <select name="rider_id" class="form-input payroll-select" required>
                        <option value="">Pilih Rider...</option>
                        @foreach($riders as $r)
                            <option value="{{ $r->id }}" {{ $selectedRiderId == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Monthly / Weekly specific: Month/Year -->
                <div class="form-group filter-monthly filter-weekly" style="flex: 1; min-width: 130px; margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        Bulan
                    </label>
                    <select name="month" id="month" class="form-input payroll-select" onchange="updateWeeklyOptions()">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="form-group filter-monthly filter-weekly" style="flex: 1; min-width: 110px; margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Tahun
                    </label>
                    <select name="year" id="year" class="form-input payroll-select" onchange="updateWeeklyOptions()">
                        @for($i = date('Y'); $i >= 2023; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Weekly specific: Week Start Selection -->
                <div class="form-group filter-weekly" style="flex: 2; min-width: 220px; margin-bottom: 0;">
                    <label class="form-label"
                        style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.6;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Minggu
                    </label>
                    <select name="week_start" id="week_start" class="form-input payroll-select">
                        <!-- Populated by JS -->
                    </select>
                </div>

                <!-- Custom specific: Date Range -->
                <div class="form-group filter-custom" style="flex: 1; min-width: 140px; margin-bottom: 0;">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary);">Dari
                        Tanggal</label>
                    <div class="input-with-icon-wrapper">
                        <input type="text" name="date_from" value="{{ request('date_from') }}" class="form-input datepicker"
                            placeholder="Pilih Tanggal">
                        <svg width="1.1em" height="1.1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                </div>

                <div class="form-group filter-custom" style="flex: 1; min-width: 140px; margin-bottom: 0;">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary);">Sampai
                        Tanggal</label>
                    <div class="input-with-icon-wrapper">
                        <input type="text" name="date_to" value="{{ request('date_to') }}" class="form-input datepicker"
                            placeholder="Pilih Tanggal">
                        <svg width="1.1em" height="1.1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary"
                        style="height: 40px; padding: 0 20px; font-weight: 600; border-radius: 10px;">Tampilkan</button>
                </div>
                </form>
                </div>
                </div>

                @if($payrollData)
                    {{-- Existing Payroll Notice --}}
                    @if($payrollData['existingPayroll'])
                        <div class="card mb-4 print-hide"
                            style="background: {{ $payrollData['existingPayroll']->status === 'confirmed' ? 'rgba(34, 197, 94, 0.08)' : 'rgba(234, 179, 8, 0.08)' }}; border: 1px solid {{ $payrollData['existingPayroll']->status === 'confirmed' ? 'rgba(34, 197, 94, 0.3)' : 'rgba(234, 179, 8, 0.3)' }};">
                            <div class="card-body" style="display: flex; align-items: center; gap: 12px; padding: 16px 20px;">
                                @if($payrollData['existingPayroll']->status === 'confirmed')
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <div>
                                        <strong style="color: #166534;">Slip gaji untuk periode ini sudah dikonfirmasi.</strong>
                                        <span style="color: #64748b; margin-left: 8px;">
                                            <a href="{{ route('admin.payroll.show', $payrollData['existingPayroll']->id) }}"
                                                style="color: #8b5c2a;">Lihat Detail &rarr;</a>
                                        </span>
                                    </div>
                                @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#eab308" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    <div>
                                        <strong style="color: #854d0e;">Slip gaji draft sudah ada untuk periode ini.</strong>
                                        <span style="color: #64748b; margin-left: 8px;">
                                            <a href="{{ route('admin.payroll.show', $payrollData['existingPayroll']->id) }}"
                                                style="color: #8b5c2a;">Lihat & Konfirmasi &rarr;</a>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Custom Payment Form (Simpan Slip) --}}
                    <div class="card mb-4 print-hide" style="margin-bottom: 8px;">
                        <div class="card-header">
                            <h3 class="card-title">Simpan Slip Gaji &mdash; Custom Pembayaran</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.payroll.store') }}" id="payroll-form">
                                @csrf
                                <input type="hidden" name="rider_id" value="{{ $payrollData['rider']->id }}">
                                <input type="hidden" name="filter_type" value="{{ $payrollData['filter_type'] }}">
                                <input type="hidden" name="period_start" value="{{ $payrollData['start_date']->format('Y-m-d') }}">
                                <input type="hidden" name="period_end" value="{{ $payrollData['end_date']->format('Y-m-d') }}">

                                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
                                    {{-- Gaji Kotor --}}
                                    <div
                                        style="flex: 1; min-width: 200px; padding: 16px; background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 12px;">
                                        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 4px;">Gaji Kotor</div>
                                        <div style="font-size: 1.3rem; font-weight: 700; color: #22c55e;" id="display-gross">Rp
                                            {{ number_format($payrollData['grossIncome'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    {{-- Outstanding Kasbon --}}
                                    <div
                                        style="flex: 1; min-width: 200px; padding: 16px; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px;">
                                        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 4px;">Outstanding
                                            Kasbon</div>
                                        <div style="font-size: 1.3rem; font-weight: 700; color: #ef4444;">Rp
                                            {{ number_format($payrollData['outstandingKasbon'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    {{-- Outstanding Minus --}}
                                    <div
                                        style="flex: 1; min-width: 200px; padding: 16px; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px;">
                                        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 4px;">Outstanding Minus
                                        </div>
                                        <div style="font-size: 1.3rem; font-weight: 700; color: #ef4444;">Rp
                                            {{ number_format($payrollData['outstandingMinus'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
                                    <div class="form-group" style="flex: 1; min-width: 250px;">
                                        <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Bayar Kasbon (Rp) <small
                                                style="color: #64748b; font-weight: 500;">Max: Rp
                                                {{ number_format($payrollData['outstandingKasbon'], 0, ',', '.') }}</small></label>
                                        <div class="input-with-icon-wrapper">
                                            <input type="number" name="kasbon_deducted" id="kasbon_deducted" class="form-input" min="0"
                                                max="{{ $payrollData['outstandingKasbon'] }}"
                                                value="{{ $payrollData['outstandingKasbon'] }}" oninput="recalculateTHP()">
                                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="form-group" style="flex: 1; min-width: 250px;">
                                        <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Bayar Minus (Rp) <small
                                                style="color: #64748b; font-weight: 500;">Max: Rp
                                                {{ number_format($payrollData['outstandingMinus'], 0, ',', '.') }}</small></label>
                                        <div class="input-with-icon-wrapper">
                                            <input type="number" name="minus_deducted" id="minus_deducted" class="form-input" min="0"
                                                max="{{ $payrollData['outstandingMinus'] }}" value="{{ $payrollData['outstandingMinus'] }}"
                                                oninput="recalculateTHP()">
                                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="8" y1="12" x2="16" y2="12"></line>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; align-items: center;">
                                    <div
                                        style="flex: 1; min-width: 300px; padding: 16px 20px; background: var(--gradient-gold); color: white; border-radius: 12px; box-shadow: 0 4px 10px rgba(139, 92, 42, 0.15);">
                                        <div style="font-size: 0.8rem; margin-bottom: 2px; opacity: 0.9; font-weight: 500;">Take Home Pay
                                            (Estimasi)</div>
                                        <div style="font-size: 1.4rem; font-weight: 700;" id="display-thp">Rp
                                            {{ number_format($payrollData['netIncome'], 0, ',', '.') }}
                                        </div>
                                        <div style="font-size: 0.75rem; margin-top: 2px; opacity: 0.8;" id="display-sisa-info">
                                            @if($payrollData['outstandingMinus'] > 0)
                                                Sisa minus carry-over: Rp <span id="sisa-minus-display">0</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group" style="flex: 1; min-width: 250px;">
                                        <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Catatan (Opsional)</label>
                                        <div class="input-with-icon-wrapper">
                                            <input type="text" name="notes" class="form-input" placeholder="Catatan tambahan...">
                                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <button type="submit" class="btn btn-primary flex-center" style="gap: 0.5rem; padding: 10px 24px;">
                                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                            <polyline points="7 3 7 8 15 8"></polyline>
                                        </svg> Simpan Slip Gaji
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card" id="payslip-card">
                        <div class="card-header print-hide" style="display: flex; justify-content: space-between; align-items: center;">
                            <h3 class="card-title">Preview Fee Mitra Rider</h3>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="window.print()" class="btn btn-secondary btn-sm flex-center" style="gap: 0.5rem;">
                                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <rect x="6" y="14" width="12" height="8"></rect>
                                    </svg> Print
                                    </button>
                                    </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Print Area -->
                                        <div class="payslip-container"
                                            style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: 'Poppins', sans-serif;">
                                            <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 20px;">
                                                <h2 style="margin: 0; color: #1e293b; font-size: 24px;">TETHER BREW</h2>
                                                <p style="margin: 5px 0 0; color: #64748b; font-size: 14px;">
                                                    @if($payrollData['filter_type'] === 'weekly')
                                                        Fee Mitra Rider (Senin - Minggu)
                                                    @elseif($payrollData['filter_type'] === 'custom')
                                                        Fee Mitra Rider Custom Range
                                                    @else
                                                        Fee Mitra Rider Akhir Bulan
                                                    @endif
                                                        </p>
                                                        <p style="margin: 5px 0 0; color: #1e293b; font-weight: 600;">
                                                            @if($payrollData['filter_type'] === 'monthly')
                                                                Periode: {{ date('F Y', mktime(0, 0, 0, $payrollData['month'], 1, $payrollData['year'])) }}
                                                            @else
                                                                Periode: {{ $payrollData['start_date']->format('d M Y') }} -
                                                                {{ $payrollData['end_date']->format('d M Y') }}
                                                            @endif
                                                        </p>
                                                        </div>

                                                        <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                                                            <div>
                                                                <p style="margin: 0 0 5px;"><strong>Nama Rider:</strong> {{ $payrollData['rider']->name }}</p>
                                                                <p style="margin: 0 0 5px;"><strong>ID Rider:</strong>
                                                                    #{{ str_pad($payrollData['rider']->id, 4, '0', STR_PAD_LEFT) }}</p>
                                                            </div>
                                                            <div style="text-align: right;">
                                                                @if($payrollData['includeUangMakan'])
                                                                    <p style="margin: 0 0 5px;"><strong>Target Penjualan:</strong>
                                                                        {{ number_format($payrollData['targetCup'], 0, ',', '.') }} Cup</p>
                                                                @endif
                                                                <p style="margin: 0 0 5px;"><strong>Total Cup Terjual:</strong> {{ $payrollData['totalCups'] }} Cup
                                                                    @if($payrollData['includeUangMakan'])
                                                                        <span
                                                                            style="display: inline-block; padding: 2px 6px; background: {{ $payrollData['achPercentage'] >= 100 ? '#22c55e' : '#ef4444' }}; color: white; border-radius: 4px; font-size: 0.8rem; font-weight: bold; margin-left: 5px;">ACH
                                                                            {{ rtrim(rtrim(number_format($payrollData['achPercentage'], 2, ',', '.'), '0'), ',') }}%</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                                            <thead>
                                                                <tr style="background: #f8fafc; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                                                                    <th style="padding: 12px; text-align: left; font-weight: 600;">Keterangan</th>
                                                                    <th style="padding: 12px; text-align: right; font-weight: 600;">Jumlah</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Gaji Bonus Penjualan
                                                                        <small>({{ $payrollData['totalCups'] }} Cup x Rp
                                                                            {{ number_format($payrollData['bonusPerCup'], 0, ',', '.') }})</small>
                                                                    </td>
                                                                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp
                                                                        {{ number_format($payrollData['grossIncome'], 0, ',', '.') }}
                                                                    </td>
                                                                </tr>

                                                                @if($payrollData['includeUangMakan'])
                                                                    @if($payrollData['sisaUangMakan'] >= 0)
                                                                        <tr>
                                                                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #22c55e;">Sisa Uang Makan
                                                                                <small>(Estimasi Rp {{ number_format($payrollData['earnedUangMakan'], 0, ',', '.') }} -
                                                                                    Diambil Rp {{ number_format($payrollData['totalUangMakan'], 0, ',', '.') }})</small>
                                                                            </td>
                                                                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #22c55e;">
                                                                                + Rp {{ number_format($payrollData['sisaUangMakan'], 0, ',', '.') }}</td>
                                                                        </tr>
                                                                    @else
                                                                        <tr>
                                                                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #ef4444;">
                                                                                Potongan Uang Makan <br>
                                                                                <small style="color: #64748b;">(Uang makan diambil tidak sesuai target: Estimasi Rp
                                                                                    {{ number_format($payrollData['earnedUangMakan'], 0, ',', '.') }} - Diambil Rp
                                                                                    {{ number_format($payrollData['totalUangMakan'], 0, ',', '.') }})</small>
                                                                            </td>
                                                                            <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #ef4444;">
                                                                                - Rp {{ number_format(abs($payrollData['sisaUangMakan']), 0, ',', '.') }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endif

                                                                <tr>
                                                                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #ef4444;">
                                                                        Potongan Kasbon <small>(Outstanding)</small>
                                                                    </td>
                                                                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #ef4444;">
                                                                        - Rp {{ number_format($payrollData['outstandingKasbon'], 0, ',', '.') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #ef4444;">
                                                                        Potongan Minus Penjualan <small>(Outstanding)</small>
                                                                        @if($payrollData['minusCarryOver'] > 0)
                                                                            <br><small style="color: #64748b;">&bull; Minus Penjualan: Rp
                                                                                {{ number_format($payrollData['minusPenjualan'], 0, ',', '.') }}</small>
                                                                            <br><small style="color: #64748b;">&bull; Minus Carry-Over: Rp
                                                                                {{ number_format($payrollData['minusCarryOver'], 0, ',', '.') }}</small>
                                                                        @endif
                                                                    </td>
                                                                    <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #ef4444;">
                                                                        - Rp {{ number_format($payrollData['outstandingMinus'], 0, ',', '.') }}</td>
                                                                </tr>
                                                                <tr style="background: #f1f5f9;">
                                                                    <td style="padding: 15px 12px; font-weight: 700; font-size: 16px;">TAKE HOME PAY</td>
                                                                    <td
                                                                        style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: {{ $payrollData['netIncome'] >= 0 ? '#1e293b' : '#ef4444' }};">
                                                                        Rp {{ number_format($payrollData['netIncome'], 0, ',', '.') }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <!-- Notes Section -->
                                                        <div style="background: #fefce8; border: 1px solid #fef08a; padding: 12px; border-radius: 8px; margin-bottom: 30px;">
                                                            <p style="margin: 0 0 5px; font-size: 0.85rem; font-weight: 700; color: #854d0e;">Catatan Penting:</p>
                                                            <ul style="margin: 0; padding-left: 20px; font-size: 0.8rem; color: #854d0e;">
                                                                <li>Potongan Kasbon & Minus dihitung dari sisa outstanding (total - yang sudah pernah dibayar).</li>
                                                                <li>Rider dapat memilih jumlah pembayaran kasbon/minus saat menyimpan slip.</li>
                                                                <li>Sisa minus yang belum terbayar akan otomatis carry-over ke periode berikutnya.</li>
                                                                @if($payrollData['includeUangMakan'])
                                                                    <li>Uang makan rider hanya tampil di pembayaran gaji akhir bulan.</li>
                                                                @endif
                                                            </ul>
                                                        </div>

                                                        <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                                                            <div style="text-align: center;">
                                                                <p style="margin-bottom: 60px;">Penerima (Rider),</p>
                                                                <p style="font-weight: 600;">( {{ $payrollData['rider']->name }} )</p>
                                                            </div>
                                                            <div style="text-align: center;">
                                                                <p style="margin-bottom: 60px;">Mengetahui (Admin),</p>
                                                                <p style="font-weight: 600;">( {{ auth()->user()->name }} )</p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        </div>

                                                        {{-- Monthly Weekly Recap --}}
                                                        @if($payrollData['filter_type'] === 'monthly' && count($payrollData['weeklyRecords']) > 0)
                                                            <div class="card mt-4" id="weekly-recap-card">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Rekap Slip Gaji Mingguan &mdash;
                                                                        {{ date('F Y', mktime(0, 0, 0, $payrollData['month'], 1, $payrollData['year'])) }}
                                                                    </h3>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="table-container">
                                                                        <table class="table">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Periode</th>
                                                                                    <th>Tipe</th>
                                                                                    <th style="text-align: right;">Gaji Kotor</th>
                                                                                    <th style="text-align: right;">Kasbon Dipotong</th>
                                                                                    <th style="text-align: right;">Minus Dipotong</th>
                                                                                    <th style="text-align: right;">THP</th>
                                                                                    <th>Status</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($payrollData['weeklyRecords'] as $rec)
                                                                                    <tr>
                                                                                        <td>{{ $rec->period_start->format('d/m') }} - {{ $rec->period_end->format('d/m/Y') }}</td>
                                                                                        <td><span class="badge badge-primary">{{ ucfirst($rec->type) }}</span></td>
                                                                                        <td style="text-align: right;">Rp {{ number_format($rec->gross_income, 0, ',', '.') }}</td>
                                                                                        <td style="text-align: right; color: #ef4444;">Rp
                                                                                            {{ number_format($rec->kasbon_deducted, 0, ',', '.') }}
                                                                                        </td>
                                                                                        <td style="text-align: right; color: #ef4444;">Rp
                                                                                            {{ number_format($rec->minus_deducted, 0, ',', '.') }}
                                                                                        </td>
                                                                                        <td style="text-align: right; font-weight: 600;">Rp
                                                                                            {{ number_format($rec->net_income, 0, ',', '.') }}
                                                                                        </td>
                                                                                        <td>
                                                                                            <span class="badge"
                                                                                                style="background: {{ $rec->status === 'confirmed' ? '#22c55e' : '#eab308' }}; color: white;">
                                                                                                {{ $rec->status === 'confirmed' ? 'Lunas' : 'Draft' }}
                                                                                            </span>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                @endif

                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            toggleFilterFields();
                            flatpickr(".datepicker", {
                                dateFormat: "Y-m-d",
                            });
                            recalculateTHP();
                        });

                        const grossIncome = {{ $payrollData['grossIncome'] ?? 0 }};
                        const outstandingKasbon = {{ $payrollData['outstandingKasbon'] ?? 0 }};
                        const outstandingMinus = {{ $payrollData['outstandingMinus'] ?? 0 }};
                        const uangMakanAdj = {{ ($payrollData['includeUangMakan'] ?? false) ? ($payrollData['sisaUangMakan'] ?? 0) : 0 }};

                        function recalculateTHP() {
                            const kasbonInput = document.getElementById('kasbon_deducted');
                            const minusInput = document.getElementById('minus_deducted');
                            if (!kasbonInput || !minusInput) return;

                            let kasbonPay = parseFloat(kasbonInput.value) || 0;
                            let minusPay = parseFloat(minusInput.value) || 0;

                            // Clamp to max
                            if (kasbonPay > outstandingKasbon) { kasbonPay = outstandingKasbon; kasbonInput.value = kasbonPay; }
                            if (minusPay > outstandingMinus) { minusPay = outstandingMinus; minusInput.value = minusPay; }

                            const thp = grossIncome - kasbonPay - minusPay + uangMakanAdj;
                            const sisaMinus = outstandingMinus - minusPay;

                            document.getElementById('display-thp').textContent = 'Rp ' + formatNumber(thp);

                            const sisaEl = document.getElementById('sisa-minus-display');
                            if (sisaEl) {
                                sisaEl.textContent = formatNumber(sisaMinus);
                            }
                        }

                        function formatNumber(num) {
                            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        }

                        function toggleFilterFields() {
                            const type = document.getElementById('filter_type').value;
                            const allFields = ['filter-weekly', 'filter-monthly', 'filter-custom'];

                            // Hide all
                            allFields.forEach(cls => {
                                document.querySelectorAll('.' + cls).forEach(el => el.style.display = 'none');
                            });

                            // Show specific
                            if (type === 'weekly') {
                                document.querySelectorAll('.filter-weekly').forEach(el => el.style.display = 'block');
                                updateWeeklyOptions();
                            } else if (type === 'custom') {
                                document.querySelectorAll('.filter-custom').forEach(el => el.style.display = 'block');
                            } else if (type === 'monthly') {
                                document.querySelectorAll('.filter-monthly').forEach(el => el.style.display = 'block');
                            }
                        }

                        function updateWeeklyOptions() {
                            const month = parseInt(document.getElementById('month').value);
                            const year = parseInt(document.getElementById('year').value);
                            const weekSelect = document.getElementById('week_start');
                            const selectedValue = "{{ request('week_start') }}";

                            weekSelect.innerHTML = '';

                            let currentDate = new Date(year, month - 1, 1);
                            let lastDayOfMonth = new Date(year, month, 0);

                            while (currentDate <= lastDayOfMonth) {
                                let periodStart = new Date(currentDate);
                                
                                // Find the next Sunday or the last day of the month
                                let periodEnd = new Date(currentDate);
                                while (periodEnd.getDay() !== 0 && periodEnd < lastDayOfMonth) {
                                    periodEnd.setDate(periodEnd.getDate() + 1);
                                }

                                const startValue = periodStart.toISOString().split('T')[0];
                                const endValue = periodEnd.toISOString().split('T')[0];
                                const value = startValue + '|' + endValue;
                                
                                const label = periodStart.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ' - ' +
                                              periodEnd.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

                                const option = new Option(label, value);
                                if (value === selectedValue) option.selected = true;
                                weekSelect.add(option);

                                // Move to the next day
                                currentDate = new Date(periodEnd);
                                currentDate.setDate(currentDate.getDate() + 1);
                            }
                        }
                    </script>
                    <style>
                                    /* Custom compact and elegant form inputs with SVG icons (only for text inputs) */
                                    .input-with-icon-wrapper {
                                        position: relative;
                                        display: flex;
                                        align-items: center;
                                        width: 100%;
                                    }

                                    .input-with-icon-wrapper .form-input {
                                        padding-left: 2.75rem !important;
                                        border-radius: 10px !important;
                                        height: 40px !important;
                                        font-size: 0.9rem !important;
                                        background-color: var(--bg-card) !important;
                                        border: 1px solid var(--border-color, #cbd5e1) !important;
                                        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                                    }

                                    .input-with-icon-wrapper .form-input:focus {
                                        border-color: #8b5c2a !important;
                                        box-shadow: 0 0 0 4px rgba(139, 92, 42, 0.15) !important;
                                        outline: none !important;
                                    }

                                    .input-with-icon-wrapper svg {
                                        position: absolute;
                                        left: 12px;
                                        color: var(--text-muted, #94a3b8);
                                        transition: color 0.25s ease;
                                        pointer-events: none;
                                    }

                                    .input-with-icon-wrapper .form-input:focus+svg {
                                        color: #8b5c2a !important;
                                    }

                                    /* Standalone select styling (no icon inside) */
                                    .payroll-select {
                                        border-radius: 10px !important;
                                        height: 40px !important;
                                        font-size: 0.9rem !important;
                                        padding: 0 12px !important;
                                        background-color: var(--bg-card) !important;
                                        border: 1px solid var(--border-color, #cbd5e1) !important;
                                        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                                        cursor: pointer;
                                    }

                                    .payroll-select:focus {
                                        border-color: #8b5c2a !important;
                                        box-shadow: 0 0 0 4px rgba(139, 92, 42, 0.15) !important;
                                        outline: none !important;
                                    }

                                    @media print {
                                        body * {
                                            visibility: hidden;
                                        }

                                        #payslip-card,
                                        #payslip-card * {
                                            visibility: visible;
                                        }

                                        #payslip-card {
                                            position: absolute;
                                            left: 0;
                                            top: 0;
                                            width: 100%;
                                            box-shadow: none !important;
                                            border: none !important;
                                        }

                                        .print-hide {
                                            display: none !important;
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
                                        background: white;
                                    }
                                }
                            </style>
                @endpush
@endsection
