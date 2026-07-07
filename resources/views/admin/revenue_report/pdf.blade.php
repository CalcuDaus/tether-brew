<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Omset - {{ $periodLabel }}</title>
    <style>
        @page {
            margin: 15mm 12mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #22c55e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header .brand {
            font-size: 22px;
            font-weight: 800;
            color: #22c55e;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header .subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .header .report-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 10px;
        }
        .header .period {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }
        .header .status-info {
            font-size: 9px;
            color: #64748b;
            font-style: italic;
            margin-top: 2px;
        }

        /* Summary Cards */
        .summary-container {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .summary-table td {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
        }
        .summary-table .label {
            color: #64748b;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 600;
            width: 30%;
            background: #f8fafc;
        }
        .summary-table .value {
            font-weight: 700;
            font-size: 12px;
        }
        .summary-table .value.green { color: #22c55e; }
        .summary-table .value.blue { color: #3b82f6; }
        .summary-table .value.red { color: #ef4444; }
        .summary-table .value.purple { color: #8b5cf6; }
        .summary-table .value.gold { color: #d97706; }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table thead th {
            background: #22c55e;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #16a34a;
        }
        .data-table tbody td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .data-table .text-right {
            text-align: right;
        }
        .data-table .text-center {
            text-align: center;
        }
        .data-table .bold {
            font-weight: 700;
        }
        .data-table .green { color: #22c55e; }
        .data-table .blue { color: #3b82f6; }
        .data-table .red { color: #ef4444; }
        .data-table .purple { color: #8b5cf6; }
        .data-table .muted { color: #94a3b8; }

        /* Grand Total */
        .grand-total td {
            background: #dcfce7 !important;
            font-weight: 800 !important;
            font-size: 10px !important;
            border: 2px solid #22c55e !important;
            padding: 8px 6px !important;
        }

        /* Status Badge */
        .badge-confirmed {
            color: #16a34a;
            font-weight: 700;
            font-size: 8px;
        }
        .badge-unconfirmed {
            color: #d97706;
            font-size: 8px;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
            font-size: 8px;
            color: #94a3b8;
            display: table;
            width: 100%;
        }
        .footer .left {
            display: table-cell;
            text-align: left;
        }
        .footer .right {
            display: table-cell;
            text-align: right;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="brand">Tether Brew</div>
        <div class="subtitle">Management System</div>
        <div class="report-title">LAPORAN OMSET</div>
        <div class="period">Periode: {{ $periodLabel }}</div>
        @php
            $statusLabel = match ($confirmationStatus ?? null) {
                'confirmed' => 'Sudah Dikonfirmasi',
                'unconfirmed' => 'Belum Dikonfirmasi',
                default => 'Semua Data',
            };
        @endphp
        <div class="status-info">Status: {{ $statusLabel }}</div>
    </div>

    {{-- Summary --}}
    @if($summary)
        <table class="summary-table">
            <tr>
                <td class="label">Total Omset</td>
                <td class="value gold">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</td>
                <td class="label">Jumlah Rider</td>
                <td class="value purple">{{ $summary['rider_count'] }} Rider</td>
            </tr>
            <tr>
                <td class="label">Setoran Cash (Fisik)</td>
                <td class="value green">Rp {{ number_format($summary['total_actual_setor'], 0, ',', '.') }}</td>
                <td class="label">Total Cup</td>
                <td class="value purple">{{ number_format($summary['total_cups'], 0, ',', '.') }} Pcs</td>
            </tr>
            <tr>
                <td class="label">Total QRIS</td>
                <td class="value blue">Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}</td>
                <td class="label" rowspan="2" style="vertical-align: middle;"></td>
                <td rowspan="2"></td>
            </tr>
            <tr>
                <td class="label">Total Minus</td>
                <td class="value red">Rp {{ number_format($summary['total_minus'], 0, ',', '.') }}</td>
            </tr>
        </table>
    @endif

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Tanggal</th>
                <th>Rider</th>
                <th>Cash (Target)</th>
                <th>Cash (Setor Fisik)</th>
                <th>QRIS</th>
                <th>Total Setoran</th>
                <th>Total</th>
                <th>Cups</th>
                <th>Admin</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($sales as $sale)
                @php
                    $dateStr = $sale->date->format('Y-m-d');
                    $isConfirmed = isset($confirmedDatesMap[$dateStr]);
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $sale->date->format('d/m/Y') }}</td>
                    <td class="bold">{{ $sale->rider->name }}</td>
                    <td class="text-right muted">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
                    <td class="text-right bold green">Rp {{ number_format($sale->actual_setor, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($sale->qris_amount, 0, ',', '.') }}</td>
                    <td class="text-right bold blue">Rp {{ number_format($sale->total_setoran, 0, ',', '.') }}</td>
                    <td class="text-right bold green">Rp {{ number_format($sale->total_gross_income, 0, ',', '.') }}</td>
                    <td class="text-center bold purple">{{ number_format($sale->total_cups ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $sale->admin_pemeriksa ?? ($sale->admin->name ?? '-') }}</td>
                    <td class="text-center">
                        @if($isConfirmed)
                            <span class="badge-confirmed">✓</span>
                        @else
                            <span class="badge-unconfirmed">○</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #94a3b8;">Tidak ada data penjualan.</td>
                </tr>
            @endforelse

            {{-- Grand Total --}}
            @if($summary && $sales->count() > 0)
                <tr class="grand-total">
                    <td colspan="3" style="text-align: center;">GRAND TOTAL</td>
                    <td class="text-right">Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}</td>
                    <td class="text-right green">Rp {{ number_format($summary['total_actual_setor'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($summary['total_qris'], 0, ',', '.') }}</td>
                    <td class="text-right blue">Rp {{ number_format($summary['total_actual_setor'] + $summary['total_qris'], 0, ',', '.') }}</td>
                    <td class="text-right green">Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}</td>
                    <td class="text-center purple">{{ number_format($summary['total_cups'], 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="left">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
        <div class="right">Tether Brew Management System</div>
    </div>
</body>
</html>
