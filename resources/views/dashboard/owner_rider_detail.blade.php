@extends('layouts.app')
@section('title', 'Performance: ' . $rider->name)

@section('actions')
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm flex-center" style="gap:0.5rem;">
        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
    </a>
    <button onclick="window.print()" class="btn btn-secondary btn-sm flex-center" style="gap:0.5rem;">
        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect>
        </svg> Print
    </button>
@endsection

@section('content')
<div id="print-area">
    <div class="print-title" style="display: none; text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #8b5c2a;">Laporan Performance Rider</h2>
        <h3 style="margin: 5px 0 0 0; color: #333;">{{ $rider->name }}</h3>
    </div>

{{-- Rider Info + Summary Cards --}}
<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid #8b5c2a;">
        <div class="stat-label mb-2-custom">
            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="#8b5c2a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.15em;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Rider
        </div>
        <div class="stat-value text-xl" style="color: #8b5c2a;">{{ $rider->name }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #22c55e;">
        <div class="stat-label mb-2-custom">
            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.15em;"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/></svg>
            Total Cup Terjual
        </div>
        <div class="stat-value text-xl" style="color: #22c55e;">{{ number_format($totalCups, 0, ',', '.') }} <small style="font-size:0.55em;opacity:0.7;">cup</small></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #d4a24e;">
        <div class="stat-label mb-2-custom">
            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="#d4a24e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.15em;"><circle cx="12" cy="12" r="8"/><line x1="12" x2="12" y1="16" y2="8"/><line x1="10" x2="14" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="14"/></svg>
            Total Pendapatan
        </div>
        <div class="stat-value text-xl" style="color: #d4a24e;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div class="stat-label mb-2-custom">
            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.15em;"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            Total Hari Kerja
        </div>
        <div class="stat-value text-xl" style="color: #3b82f6;">{{ $totalDays }} <small style="font-size:0.55em;opacity:0.7;">hari</small></div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid-2 mt-2-custom">
    {{-- Daily Performance Line Chart --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.25em;"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                Penjualan Harian ({{ $days }} Hari)
            </h3>
        </div>
        <div class="card-body">
            @if(count($dailyChart) > 0)
                <div id="daily-chart"></div>
            @else
                <div class="empty-state p-5-custom"><div class="empty-state-text">Belum ada data</div></div>
            @endif
        </div>
    </div>

    {{-- Payment Distribution --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.25em;"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10"/><path d="M2 12h20"/></svg>
                Metode Pembayaran
            </h3>
        </div>
        <div class="card-body">
            @if($totalCash + $totalQris > 0)
                <div id="payment-chart"></div>
            @else
                <div class="empty-state p-5-custom"><div class="empty-state-text">Belum ada data</div></div>
            @endif
        </div>
    </div>
</div>

{{-- Product Breakdown --}}
@if(count($productTotals) > 0)
<div class="card mt-2-custom">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.25em;"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Ringkasan Per Produk
        </h3>
    </div>
    <div class="card-body card-body-no-padding">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Total Terjual</th>
                        <th style="text-align:right;">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productTotals as $i => $pt)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-primary-semi" style="font-weight:600;">{{ $pt['name'] }}</td>
                        <td style="text-align:right;">Rp {{ number_format($pt['price'], 0, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($pt['total_sold'], 0, ',', '.') }} cup</td>
                        <td style="text-align:right;" class="text-gold-semi">Rp {{ number_format($pt['total_revenue'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight:700;">
                        <td colspan="3">TOTAL</td>
                        <td style="text-align:right;">{{ number_format($totalCups, 0, ',', '.') }} cup</td>
                        <td style="text-align:right;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Daily Detail Table --}}
<div class="card mt-2-custom" id="detail-card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:-0.25em;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
            Detail Penjualan Harian
        </h3>
    </div>
    <div class="card-body card-body-no-padding">
        @if($sales->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Cup Terjual</th>
                        <th style="text-align:right;">Cash</th>
                        <th style="text-align:right;">QRIS</th>
                        <th style="text-align:right;">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->date->format('d M Y') }} <small style="color:var(--text-muted);">({{ $sale->date->translatedFormat('l') }})</small></td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($sale->items->sum('stock_sold'), 0, ',', '.') }}</td>
                        <td style="text-align:right;">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
                        <td style="text-align:right;">Rp {{ number_format($sale->qris_amount, 0, ',', '.') }}</td>
                        <td style="text-align:right;font-weight:600;" class="text-gold-semi">Rp {{ number_format($sale->total_gross_income, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="font-weight:700;">
                        <td>TOTAL ({{ $totalDays }} Hari)</td>
                        <td style="text-align:right;">{{ number_format($totalCups, 0, ',', '.') }}</td>
                        <td style="text-align:right;">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
                        <td style="text-align:right;">Rp {{ number_format($totalQris, 0, ',', '.') }}</td>
                        <td style="text-align:right;font-size:1rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
            <div class="empty-state p-5-custom"><div class="empty-state-text">Belum ada data penjualan harian</div></div>
        @endif
    </div>
</div>
</div> {{-- End print-area --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isLight = document.documentElement.classList.contains('light-theme');
    const textColor = isLight ? '#475569' : '#94a3b8';
    const gridColor = isLight ? '#e2e8f0' : '#1e293b';
    const brownPrimary = '#8b5c2a';
    const greenPrimary = '#22c55e';

    // Daily Performance Chart
    @if(count($dailyChart) > 0)
    const chartData = @json($dailyChart);
    const translatedDays = {
        'Monday': 'Sen', 'Tuesday': 'Sel', 'Wednesday': 'Rab', 'Thursday': 'Kam',
        'Friday': 'Jum', 'Saturday': 'Sab', 'Sunday': 'Min'
    };

    new ApexCharts(document.querySelector("#daily-chart"), {
        series: [
            {
                name: 'Pendapatan',
                type: 'area',
                data: chartData.map(d => d.revenue)
            },
            {
                name: 'Cup Terjual',
                type: 'column',
                data: chartData.map(d => d.cups)
            }
        ],
        chart: {
            height: 350,
            fontFamily: 'Poppins, sans-serif',
            background: 'transparent',
            toolbar: { show: false },
            stacked: false
        },
        theme: { mode: isLight ? 'light' : 'dark' },
        colors: [brownPrimary, greenPrimary],
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        stroke: {
            width: [3, 0],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '50%'
            }
        },
        xaxis: {
            categories: chartData.map(d => {
                const dt = new Date(d.date);
                const dayName = dt.toLocaleDateString('en-US', { weekday: 'long' });
                return (translatedDays[dayName] || dayName.substring(0,3)) + ' ' + dt.getDate();
            }),
            labels: {
                style: { colors: textColor, fontSize: '11px' },
                rotate: -45,
                rotateAlways: chartData.length > 10
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: [
            {
                title: { text: 'Pendapatan (Rp)', style: { color: textColor } },
                labels: {
                    formatter: val => 'Rp' + (val/1000).toLocaleString('id-ID') + 'k',
                    style: { colors: textColor }
                }
            },
            {
                opposite: true,
                title: { text: 'Cup Terjual', style: { color: textColor } },
                labels: {
                    formatter: val => Math.round(val),
                    style: { colors: textColor }
                }
            }
        ],
        grid: { borderColor: gridColor, strokeDashArray: 4 },
        tooltip: {
            theme: 'dark',
            shared: true,
            intersect: false,
            y: {
                formatter: function(val, { seriesIndex }) {
                    if (seriesIndex === 0) return 'Rp ' + val.toLocaleString('id-ID');
                    return val + ' cup';
                }
            }
        },
        legend: {
            position: 'top',
            labels: { colors: textColor },
            fontSize: '13px',
            fontWeight: 600
        }
    }).render();
    @endif

    // Payment Donut
    @if($totalCash + $totalQris > 0)
    new ApexCharts(document.querySelector("#payment-chart"), {
        series: [{{ $totalCash }}, {{ $totalQris }}],
        chart: {
            type: 'donut',
            height: 350,
            fontFamily: 'Poppins, sans-serif',
            background: 'transparent',
        },
        theme: { mode: isLight ? 'light' : 'dark' },
        labels: ['Cash', 'QRIS'],
        colors: [brownPrimary, greenPrimary],
        stroke: { width: 0 },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: { show: true, fontSize: '14px', fontWeight: 600 },
                        value: {
                            show: true,
                            fontSize: '18px',
                            fontWeight: 700,
                            formatter: val => 'Rp ' + parseInt(val).toLocaleString('id-ID')
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '14px',
                            color: textColor,
                            formatter: function(w) {
                                return 'Rp ' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: val => val.toFixed(1) + '%',
            style: { fontSize: '13px', fontWeight: 700 }
        },
        legend: {
            position: 'bottom',
            fontSize: '14px',
            fontWeight: 600,
            labels: { colors: textColor },
            markers: { radius: 4, width: 12, height: 12 }
        },
        tooltip: {
            y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') }
        }
    }).render();
    @endif

    window.addEventListener('theme-changed', () => location.reload());
});
</script>
@endpush

@push('styles')
<style>
    @media print {
        @page { margin: 15mm; }
        body { font-size: 11pt; }
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 10px; box-sizing: border-box; }
        .print-title { display: block !important; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0; margin-bottom: 25px !important; }
        .topbar, .sidebar, .btn, .print-hide { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; break-inside: avoid; margin-bottom: 25px !important; border-radius: 8px !important; }
        .card-header { background: transparent !important; border-bottom: 1px solid #cbd5e1 !important; padding: 15px 20px !important; }
        .card-body { padding: 15px 20px !important; }
        .stats-grid { page-break-inside: avoid; gap: 15px !important; }
        .stat-card { border: 1px solid #cbd5e1 !important; border-left-width: 5px !important; padding: 15px !important; border-radius: 8px !important; }
        #daily-chart, #payment-chart { max-height: 250px; }
        
        /* Ensure table prints well */
        table { width: 100% !important; border-collapse: collapse !important; }
        th { background-color: #f8fafc !important; color: #1e293b !important; border-bottom: 2px solid #cbd5e1 !important; padding: 12px 10px !important; font-weight: bold !important; font-size: 10pt !important; }
        td { border-bottom: 1px solid #e2e8f0 !important; padding: 10px !important; font-size: 10.5pt !important; }
        tfoot td { border-bottom: none !important; border-top: 2px solid #cbd5e1 !important; }
    }
</style>
@endpush
@endsection
