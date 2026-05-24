@extends('layouts.app')
@section('title', 'Dashboard Owner')

@section('content')
{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon gold">
            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
            </svg>
        </div>
        <div class="stat-value">{{ $totalCarts }}</div>
        <div class="stat-label">Total Gerobak</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-value">{{ $activeCarts }}</div>
        <div class="stat-label">Gerobak Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="8"/><line x1="12" x2="12" y1="16" y2="8"/><line x1="10" x2="14" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="14"/>
            </svg>
        </div>
        <div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
        <div class="stat-label">Pendapatan Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
            </svg>
        </div>
        <div class="stat-value">{{ $todayTransactions }}</div>
        <div class="stat-label">Transaksi Hari Ini</div>
    </div>
</div>

{{-- Revenue Summary --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label mb-2-custom"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg> Hari Ini</div>
        <div class="stat-value text-xl">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label mb-2-custom"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg> Minggu Ini</div>
        <div class="stat-value text-xl">Rp {{ number_format($weekRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label mb-2-custom"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg> Bulan Ini</div>
        <div class="stat-value text-xl">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
    </div>
</div>

<div class="grid-2 mt-2-custom">
    {{-- Top Products --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg> Produk Terlaris (30 Hari)</h3>
        </div>
        <div class="card-body card-body-no-padding">
            @if($topProducts->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produk</th>
                            <th>Terjual</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-primary-semi">{{ $item->product->name ?? '-' }}</td>
                                <td>{{ $item->total_sold }} cup</td>
                                <td class="text-gold-semi">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state p-5-custom">
                    <div class="empty-state-icon"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg></div>
                    <div class="empty-state-text">Belum ada data penjualan</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Daily Revenue Chart --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg> Pendapatan 7 Hari Terakhir</h3>
        </div>
        <div class="card-body">
            @if($dailyRevenue->count() > 0)
                <div id="revenue-chart"></div>
            @else
                <div class="empty-state p-5-custom">
                    <div class="empty-state-icon"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></div>
                    <div class="empty-state-text">Belum ada data pendapatan</div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($dailyRevenue->count() > 0)
            const dailyData = @json($dailyRevenue);
            const translatedDays = {
                'Monday': 'Sen', 'Tuesday': 'Sel', 'Wednesday': 'Rab', 'Thursday': 'Kam',
                'Friday': 'Jum', 'Saturday': 'Sab', 'Sunday': 'Min'
            };

            const series = [{
                name: 'Pendapatan',
                data: dailyData.map(item => item.revenue)
            }];

            const categories = dailyData.map(item => {
                const dayName = new Date(item.date).toLocaleDateString('en-US', { weekday: 'long' });
                return translatedDays[dayName] || dayName.substring(0, 3);
            });

            const isLightTheme = document.documentElement.classList.contains('light-theme');

            const options = {
                series: series,
                chart: {
                    type: 'bar',
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    background: 'transparent',
                    toolbar: { show: false },
                    borderRadius: 8
                },
                theme: {
                    mode: isLightTheme ? 'light' : 'dark'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '60%',
                        distributed: true,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                colors: ['#22c55e'], 
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return "Rp" + (val / 1000).toLocaleString('id-ID') + "k";
                    },
                    offsetY: -25,
                    style: {
                        colors: ['#22c55e'],
                        fontSize: '12px',
                        fontWeight: 700
                    }
                },
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: isLightTheme ? '#64748b' : '#94a3b8',
                            fontWeight: 600
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return "Rp" + (val/1000).toLocaleString('id-ID') + "k";
                        },
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                grid: {
                    borderColor: isLightTheme ? '#e2e8f0' : '#1e293b',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return "Rp " + val.toLocaleString('id-ID');
                        }
                    }
                },
                legend: { show: false }
            };

            const chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
            chart.render();
        @endif
    });
</script>
@endpush

{{-- Recent Transactions --}}
<div class="card mt-5-custom">
    <div class="card-header">
        <h3 class="card-title"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Transaksi Terbaru</h3>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">Lihat Semua →</a>
    </div>
    <div class="card-body card-body-no-padding">
        @if($recentTransactions->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gerobak</th>
                            <th>Rider</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $tx)
                            <tr>
                                <td>#{{ $tx->id }}</td>
                                <td class="text-primary-semi">{{ $tx->cart->name ?? '-' }}</td>
                                <td>{{ $tx->user->name ?? '-' }}</td>
                                <td class="text-gold-semi">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td><span class="badge badge-{{ $tx->payment_method }}">{{ strtoupper($tx->payment_method) }}</span></td>
                                <td>{{ $tx->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon"><svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.25em;"><circle cx="12" cy="12" r="8"/><line x1="12" x2="12" y1="16" y2="8"/><line x1="10" x2="14" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="14"/></svg></div>
                <div class="empty-state-text">Belum ada transaksi</div>
            </div>
        @endif
    </div>
</div>
@endsection

