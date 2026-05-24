@extends('layouts.app')

@section('title', 'Laporan Penjualan Rider')

@section('content')
<div class="card mb-4 print-hide">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan Penjualan</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.rider_sales_report.index') }}" id="filter-form" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 180px;">
                <label class="form-label">Tipe Filter</label>
                <select name="filter_type" id="filter_type" class="form-input" onchange="toggleFilterFields()">
                    <option value="weekly" {{ $filterType === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="custom" {{ $filterType === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    <option value="monthly" {{ $filterType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>

            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label class="form-label">Rider</label>
                <select name="rider_id" class="form-input" required>
                    <option value="">Pilih Rider...</option>
                    @foreach($riders as $r)
                        <option value="{{ $r->id }}" {{ $selectedRiderId == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Monthly / Weekly specific: Month/Year -->
            <div class="form-group filter-monthly filter-weekly" style="flex: 1; min-width: 140px;">
                <label class="form-label">Bulan</label>
                <select name="month" id="month" class="form-input" onchange="updateWeeklyOptions()">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="form-group filter-monthly filter-weekly" style="flex: 1; min-width: 120px;">
                <label class="form-label">Tahun</label>
                <select name="year" id="year" class="form-input" onchange="updateWeeklyOptions()">
                    @for($i=date('Y'); $i>=2023; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Weekly specific: Week Start Selection -->
            <div class="form-group filter-weekly" style="flex: 1; min-width: 250px;">
                <label class="form-label">Pilih Minggu (Senin - Minggu)</label>
                <select name="week_start" id="week_start" class="form-input">
                    <!-- Populated by JS -->
                </select>
            </div>

            <!-- Custom specific: Date Range -->
            <div class="form-group filter-custom" style="flex: 1; min-width: 150px;">
                <label class="form-label">Dari Tanggal</label>
                <input type="text" name="date_from" value="{{ request('date_from') }}" class="form-input datepicker" placeholder="Pilih Tanggal">
            </div>

            <div class="form-group filter-custom" style="flex: 1; min-width: 150px;">
                <label class="form-label">Sampai Tanggal</label>
                <input type="text" name="date_to" value="{{ request('date_to') }}" class="form-input datepicker" placeholder="Pilih Tanggal">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

@if($reportData)
<div class="card" id="report-card">
    <div class="card-header print-hide" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Laporan Penjualan Rider</h3>
        <button onclick="window.print()" class="btn btn-secondary btn-sm flex-center" style="gap: 0.5rem;">
            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect>
            </svg> Print Laporan
        </button>
    </div>
    <div class="card-body">
        <div class="report-container" style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: 'Inter', sans-serif;">
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 20px;">
                <h2 style="margin: 0; color: #1e293b; font-size: 24px;">TETHER BREW</h2>
                <p style="margin: 5px 0 0; color: #64748b; font-size: 14px;">
                    Laporan Penjualan Rider
                    @if($reportData['filter_type'] === 'weekly')
                        (Mingguan)
                    @elseif($reportData['filter_type'] === 'custom')
                        (Custom Range)
                    @else
                        (Bulanan)
                    @endif
                </p>
                <p style="margin: 5px 0 0; color: #1e293b; font-weight: 600;">
                    @if($reportData['filter_type'] === 'monthly')
                        Periode: {{ date('F Y', mktime(0,0,0,$reportData['month'],1,$reportData['year'])) }}
                    @else
                        Periode: {{ $reportData['start_date']->format('d M Y') }} - {{ $reportData['end_date']->format('d M Y') }}
                    @endif
                </p>
            </div>

            <!-- Rider Info -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
                <div>
                    <p style="margin: 0 0 5px;"><strong>Nama Rider:</strong> {{ $reportData['rider']->name }}</p>
                    <p style="margin: 0 0 5px;"><strong>ID Rider:</strong> #{{ str_pad($reportData['rider']->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0 0 5px;"><strong>Total Hari Kerja:</strong> {{ $reportData['totalDays'] }} Hari</p>
                    <p style="margin: 0 0 5px;"><strong>Total Cup Terjual:</strong> {{ number_format($reportData['grandTotalCups'], 0, ',', '.') }} Cup</p>
                </div>
            </div>

            <!-- Detail Penjualan Harian -->
            <h4 style="margin: 0 0 10px; color: #1e293b; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Detail Penjualan Harian</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 0.85rem;">
                <thead>
                    <tr style="background: #f8fafc; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 10px 8px; text-align: left; font-weight: 600;">Tanggal</th>
                        <th style="padding: 10px 8px; text-align: right; font-weight: 600;">Cup Terjual</th>
                        <th style="padding: 10px 8px; text-align: right; font-weight: 600;">Cash</th>
                        <th style="padding: 10px 8px; text-align: right; font-weight: 600;">QRIS</th>
                        <th style="padding: 10px 8px; text-align: right; font-weight: 600;">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['sales'] as $sale)
                    <tr>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0;">{{ $sale->date->format('d M Y') }} <small style="color: #94a3b8;">({{ $sale->date->translatedFormat('l') }})</small></td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">{{ number_format($sale->items->sum('stock_sold'), 0, ',', '.') }}</td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp {{ number_format($sale->qris_amount, 0, ',', '.') }}</td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600;">Rp {{ number_format($sale->total_gross_income, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: 700;">
                        <td style="padding: 12px 8px;">TOTAL</td>
                        <td style="padding: 12px 8px; text-align: right;">{{ number_format($reportData['grandTotalCups'], 0, ',', '.') }}</td>
                        <td style="padding: 12px 8px; text-align: right;">Rp {{ number_format($reportData['grandTotalCash'], 0, ',', '.') }}</td>
                        <td style="padding: 12px 8px; text-align: right;">Rp {{ number_format($reportData['grandTotalQris'], 0, ',', '.') }}</td>
                        <td style="padding: 12px 8px; text-align: right; font-size: 1rem;">Rp {{ number_format($reportData['grandTotalRevenue'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Ringkasan Per Produk -->
            <h4 style="margin: 0 0 10px; color: #1e293b; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Ringkasan Per Produk</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 0.85rem;">
                <thead>
                    <tr style="background: #f8fafc; border-top: 2px solid #cbd5e1; border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 10px 8px; text-align: left; font-weight: 600;">Produk</th>
                        <th style="padding: 10px 8px; text-align: right; font-weight: 600;">Harga Satuan</th>
                        <th style="padding: 10px 8px; text-align: right; font-weight: 600;">Total Terjual</th>
                        <th style="padding: 10px 8px; text-align: right; font-weight: 600;">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['productTotals'] as $pt)
                    <tr>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0;">{{ $pt['name'] }}</td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp {{ number_format($pt['price'], 0, ',', '.') }}</td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: right;">{{ number_format($pt['total_sold'], 0, ',', '.') }} Cup</td>
                        <td style="padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600;">Rp {{ number_format($pt['total_revenue'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: 700;">
                        <td style="padding: 12px 8px;" colspan="2">TOTAL</td>
                        <td style="padding: 12px 8px; text-align: right;">{{ number_format($reportData['grandTotalCups'], 0, ',', '.') }} Cup</td>
                        <td style="padding: 12px 8px; text-align: right; font-size: 1rem;">Rp {{ number_format($reportData['grandTotalRevenue'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Ringkasan Pembayaran -->
            <h4 style="margin: 0 0 10px; color: #1e293b; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Ringkasan Pembayaran</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <tbody>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Total Pembayaran Cash</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp {{ number_format($reportData['grandTotalCash'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Total Pembayaran QRIS</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;">Rp {{ number_format($reportData['grandTotalQris'], 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background: #f1f5f9;">
                        <td style="padding: 15px 12px; font-weight: 700; font-size: 16px;">TOTAL PENDAPATAN</td>
                        <td style="padding: 15px 12px; text-align: right; font-weight: 700; font-size: 18px; color: #1e293b;">Rp {{ number_format($reportData['grandTotalRevenue'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Signature Area -->
            <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                <div style="text-align: center;">
                    <p style="margin-bottom: 60px;">Rider,</p>
                    <p style="font-weight: 600;">( {{ $reportData['rider']->name }} )</p>
                </div>
                <div style="text-align: center;">
                    <p style="margin-bottom: 60px;">Mengetahui (Admin),</p>
                    <p style="font-weight: 600;">( {{ auth()->user()->name }} )</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterFields();
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
        });
    });

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
        
        // Find all Mondays in the month
        let date = new Date(year, month - 1, 1);
        
        // Move to first Monday
        while (date.getDay() !== 1) {
            date.setDate(date.getDate() + 1);
        }
        
        while (date.getMonth() === month - 1) {
            const monday = new Date(date);
            const sunday = new Date(date);
            sunday.setDate(monday.getDate() + 6);
            
            const value = monday.toISOString().split('T')[0];
            const label = monday.toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}) + ' - ' + 
                          sunday.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});
            
            const option = new Option(label, value);
            if (value === selectedValue) option.selected = true;
            weekSelect.add(option);
            
            date.setDate(date.getDate() + 7);
        }
    }
</script>
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #report-card, #report-card * {
            visibility: visible;
        }
        #report-card {
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
        .main-content { margin: 0; padding: 0; }
        .sidebar { display: none; }
        .topbar { display: none; }
        body { background: white; }
    }
</style>
@endpush
@endsection
