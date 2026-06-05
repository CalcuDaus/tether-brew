@extends('layouts.app')

@section('title', 'Detail Produksi Harian')

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="card mb-4 print-area" style="max-width: 900px; margin: 0 auto; background: var(--bg-card);">
    <div class="card-header" style="text-align: center; border-bottom: 2px solid var(--border-color); padding: 20px; position: relative;">
        <h2 style="font-weight: 800; color: var(--text-primary); margin: 0; font-size: 1.8rem; letter-spacing: 1px;">DETAIL STOK PRODUKSI</h2>
        <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 1rem;">Tether Brew Office</p>
    </div>

    <div class="card-body" style="padding: 30px;">
        <!-- Info Section -->
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
            <div>
                <p style="margin: 0 0 5px; font-weight: 600; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">Tanggal Produksi</p>
                <p style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--text-primary);">{{ $production->date->format('d F Y') }}</p>
            </div>
            <div>
                <p style="margin: 0 0 5px; font-weight: 600; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">Diinput Oleh</p>
                <p style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--text-primary);">{{ $production->user->name }}</p>
            </div>
            <div>
                <p style="margin: 0 0 5px; font-weight: 600; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase;">Cabang</p>
                <p style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--text-primary);">{{ $production->branch->name ?? 'Pusat' }}</p>
            </div>
        </div>

        <!-- Table Section -->
        <h4 style="font-weight: 700; margin-bottom: 15px; color: var(--text-primary); font-size: 1.2rem;">Rincian Produk</h4>
        <div class="table-container no-search" style="border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; margin-bottom: 30px;">
            <table class="table" style="margin: 0; width: 100%;">
                <thead style="background: rgba(0,0,0,0.03);">
                    <tr>
                        <th style="padding: 15px; font-weight: 700; text-align: left; border-bottom: 2px solid var(--border-color);">No</th>
                        <th style="padding: 15px; font-weight: 700; text-align: left; border-bottom: 2px solid var(--border-color);">Nama Produk</th>
                        <th style="padding: 15px; font-weight: 700; text-align: center; border-bottom: 2px solid var(--border-color);">Jumlah Diproduksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @forelse($production->items as $index => $item)
                        @php $total += $item->quantity_produced; @endphp
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px; color: var(--text-primary);">{{ $index + 1 }}</td>
                            <td style="padding: 15px; font-weight: 600; color: var(--text-primary);">{{ $item->product->name }}</td>
                            <td style="padding: 15px; text-align: center; font-weight: 600; color: #3b82f6;">
                                <span style="background: rgba(59,130,246,0.1); padding: 5px 15px; border-radius: 20px;">
                                    {{ number_format($item->quantity_produced, 0, ',', '.') }} Cup
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 20px; text-align: center; color: var(--text-muted);">Tidak ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: rgba(0,0,0,0.02);">
                        <td colspan="2" style="padding: 15px; text-align: right; font-weight: 800; font-size: 1.1rem;">Total Keseluruhan :</td>
                        <td style="padding: 15px; text-align: center; font-weight: 800; font-size: 1.2rem; color: #22c55e;">{{ number_format($total, 0, ',', '.') }} Cup</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="no-print" style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 40px;">
            <a href="{{ route('admin.productions.index') }}" class="btn btn-secondary" style="height: 44px; padding: 0 25px; display: flex; align-items: center; font-weight: 600; gap: 8px;">
                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary" style="height: 44px; padding: 0 25px; display: flex; align-items: center; font-weight: 600; gap: 8px; background-color: #0ea5e9; border-color: #0ea5e9;">
                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Print Out
            </button>
        </div>
    </div>
</div>
@endsection
