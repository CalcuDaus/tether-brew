@extends('layouts.app')

@section('title', 'Detail Slip Gaji')

@section('content')
{{-- Status Banner --}}
<div class="card mb-4 print-hide" style="background: {{ $payrollRecord->status === 'confirmed' ? 'rgba(34, 197, 94, 0.08)' : 'rgba(234, 179, 8, 0.08)' }}; border: 1px solid {{ $payrollRecord->status === 'confirmed' ? 'rgba(34, 197, 94, 0.3)' : 'rgba(234, 179, 8, 0.3)' }};">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            @if($payrollRecord->status === 'confirmed')
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <div>
                    <strong style="color: #166534;">Dikonfirmasi</strong>
                    <span style="color: #64748b; margin-left: 8px;">{{ $payrollRecord->confirmed_at->translatedFormat('d M Y, H:i') }} WIB oleh {{ $payrollRecord->confirmedByAdmin->name ?? '-' }}</span>
                </div>
            @else
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#eab308" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <div>
                    <strong style="color: #854d0e;">Draft &mdash; Belum dikonfirmasi</strong>
                    <span style="color: #64748b; margin-left: 8px;">Dibuat: {{ $payrollRecord->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                </div>
            @endif
        </div>
        <div style="display: flex; gap: 8px;">
            @if($payrollRecord->status === 'draft')
                <form method="POST" action="{{ route('admin.payroll.confirm', $payrollRecord->id) }}" data-confirm="Konfirmasi slip gaji ini? Sisa minus/kasbon yang belum terbayar akan di-carry-over ke periode berikutnya.">
                    @csrf
                    <button type="submit" class="btn btn-primary flex-center" style="gap: 0.5rem;">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        Konfirmasi Pembayaran
                    </button>
                </form>
            @endif
            @if($payrollRecord->status === 'confirmed')
                <form method="POST" action="{{ route('admin.payroll.rollback', $payrollRecord->id) }}" data-confirm="Batalkan konfirmasi slip gaji ini? Saldo minus/kasbon serta Jurnal Umum yang berhubungan akan dibatalkan/di-revert.">
                    @csrf
                    <button type="submit" class="btn btn-secondary flex-center" style="gap: 0.5rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2);">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><polyline points="3 3 3 8 8 8"></polyline></svg>
                        Batalkan Konfirmasi
                    </button>
                </form>
            @endif
            <button onclick="window.print()" class="btn btn-secondary flex-center" style="gap: 0.5rem;">
                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect>
                </svg> Print Slip Gaji
            </button>
            <a href="{{ route('admin.payroll.history') }}" class="btn btn-secondary flex-center" style="gap: 0.5rem;">
                &larr; Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 10px;" id="payslip-card">
    <div class="card-body">
        <div class="payslip-container" style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: 'Poppins', sans-serif;">
            <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px dashed #cbd5e1; padding-bottom: 20px;">
                <img src="{{ asset('tether-icon-head.webp') }}" alt="Logo Tether Brew" style="height: 60px; width: auto; display: block; margin: 0 auto 10px auto;">
                <h2 style="color: #22c55e; font-size: 1.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0;">TETHER BREW</h2>
                <p style="margin: 5px 0 0; color: #64748b; font-size: 10pt;">
                    @if($payrollRecord->type === 'weekly')
                        Slip Gaji Mingguan (Senin - Minggu)
                    @elseif($payrollRecord->type === 'custom')
                        Slip Gaji Custom Range
                    @else
                        Slip Gaji Akhir Bulan
                    @endif
                    @if($payrollRecord->status === 'confirmed')
                        <span style="display: inline-block; padding: 2px 8px; background: #22c55e; color: white; border-radius: 4px; font-size: 0.75rem; font-weight: bold; margin-left: 8px;">LUNAS</span>
                    @else
                        <span style="display: inline-block; padding: 2px 8px; background: #eab308; color: white; border-radius: 4px; font-size: 0.75rem; font-weight: bold; margin-left: 8px;">DRAFT</span>
                    @endif
                </p>
                <p style="margin: 5px 0 0; color: #1e293b; font-weight: 600;">
                    Periode: {{ $payrollRecord->period_start->format('d M Y') }} - {{ $payrollRecord->period_end->format('d M Y') }}
                </p>
                <p style="margin: 3px 0 0; color: #94a3b8; font-size: 12px;">
                    Slip #{{ str_pad($payrollRecord->id, 5, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                <div>
                    <p style="margin: 0 0 5px;"><strong>Nama Rider:</strong> {{ $payrollRecord->rider->name }}</p>
                    <p style="margin: 0 0 5px;"><strong>ID Rider:</strong> #{{ str_pad($payrollRecord->rider->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0 0 5px;"><strong>Total Cup Terjual:</strong> {{ $payrollRecord->total_cups }} Cup</p>
                    <p style="margin: 0 0 5px;"><strong>Admin:</strong> {{ $payrollRecord->admin->name }}</p>
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
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Gaji Bonus Penjualan <small>({{ $payrollRecord->total_cups }} Cup x Rp {{ number_format($bonusPerCup, 0, ',', '.') }})</small></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp {{ number_format($payrollRecord->gross_income, 0, ',', '.') }}</td>
                    </tr>
                    
                    @if($payrollRecord->uang_makan_adjustment != 0)
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: {{ $payrollRecord->uang_makan_adjustment >= 0 ? '#22c55e' : '#ef4444' }};">
                            {{ $payrollRecord->uang_makan_adjustment >= 0 ? 'Sisa Uang Makan' : 'Potongan Uang Makan' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: {{ $payrollRecord->uang_makan_adjustment >= 0 ? '#22c55e' : '#ef4444' }};">
                            {{ $payrollRecord->uang_makan_adjustment >= 0 ? '+' : '-' }} Rp {{ number_format(abs($payrollRecord->uang_makan_adjustment), 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #ef4444;">
                            Potongan Kasbon
                            <br><small style="color: #64748b;">Outstanding: Rp {{ number_format($payrollRecord->kasbon_outstanding, 0, ',', '.') }} &mdash; Dibayar: Rp {{ number_format($payrollRecord->kasbon_deducted, 0, ',', '.') }}</small>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #ef4444;">- Rp {{ number_format($payrollRecord->kasbon_deducted, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #ef4444;">
                            Potongan Minus Penjualan
                            <br><small style="color: #64748b;">Outstanding: Rp {{ number_format($payrollRecord->minus_outstanding, 0, ',', '.') }} &mdash; Dibayar: Rp {{ number_format($payrollRecord->minus_deducted, 0, ',', '.') }}</small>
                            @php $sisaMinus = $payrollRecord->minus_outstanding - $payrollRecord->minus_deducted; @endphp
                            @if($sisaMinus > 0)
                                <br><small style="color: #eab308;">&#9888; Sisa carry-over: Rp {{ number_format($sisaMinus, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; color: #ef4444;">- Rp {{ number_format($payrollRecord->minus_deducted, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background: #f1f5f9;">
                        <td style="padding: 15px 12px; font-weight: 700; font-size: 16px;">TAKE HOME PAY</td>
                        <td style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: {{ $payrollRecord->net_income >= 0 ? '#1e293b' : '#ef4444' }};">Rp {{ number_format($payrollRecord->net_income, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            @if($payrollRecord->notes)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px; margin-bottom: 30px;">
                <p style="margin: 0; font-size: 0.85rem;"><strong>Catatan:</strong> {{ $payrollRecord->notes }}</p>
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                <div style="text-align: center;">
                    <p style="margin-bottom: 60px;">Penerima (Rider),</p>
                    <p style="font-weight: 600;">( {{ $payrollRecord->rider->name }} )</p>
                </div>
                <div style="text-align: center;">
                    <p style="margin-bottom: 60px;">Mengetahui (Admin),</p>
                    <p style="font-weight: 600;">( {{ $payrollRecord->admin->name }} )</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Monthly Weekly Recap --}}
@if($payrollRecord->type === 'monthly' && count($weeklyRecords) > 0)
<div class="card mt-4" id="weekly-recap-card">
    <div class="card-header">
        <h3 class="card-title">Rekap Slip Gaji Mingguan</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th style="text-align: right;">Gaji Kotor</th>
                        <th style="text-align: right;">Kasbon</th>
                        <th style="text-align: right;">Minus</th>
                        <th style="text-align: right;">THP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalWeeklyTHP = 0; @endphp
                    @foreach($weeklyRecords as $rec)
                    @php $totalWeeklyTHP += $rec->net_income; @endphp
                    <tr>
                        <td>{{ $rec->period_start->format('d/m') }} - {{ $rec->period_end->format('d/m/Y') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($rec->gross_income, 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #ef4444;">Rp {{ number_format($rec->kasbon_deducted, 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #ef4444;">Rp {{ number_format($rec->minus_deducted, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 600;">Rp {{ number_format($rec->net_income, 0, ',', '.') }}</td>
                        <td><span class="badge" style="background: {{ $rec->status === 'confirmed' ? '#22c55e' : '#eab308' }}; color: white;">{{ $rec->status === 'confirmed' ? 'Lunas' : 'Draft' }}</span></td>
                    </tr>
                    @endforeach
                    <tr style="background: #f1f5f9; font-weight: 700;">
                        <td colspan="4" style="padding: 12px;">Total THP Mingguan</td>
                        <td style="text-align: right; padding: 12px;">Rp {{ number_format($totalWeeklyTHP, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@push('scripts')
<style>
    @media print {
        body * { visibility: hidden; }
        #payslip-card, #payslip-card * { visibility: visible; }
        #payslip-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; }
        .print-hide { display: none !important; }
        .main-content { margin: 0; padding: 0; }
        .sidebar { display: none; }
        .topbar { display: none; }
        body { background: white; }
    }
</style>
@endpush
@endsection
