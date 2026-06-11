@extends('layouts.app')

@section('title', 'Uang Makan Rider')

@section('actions')
    <button onclick="window.print()" class="btn btn-secondary btn-sm flex-center" style="gap:0.5rem;">
        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect>
        </svg> Print
    </button>
@endsection

@section('content')
<div id="print-area">
    <div class="print-title print-only" style="display: none;">
        <img src="{{ asset('tether-icon-head.webp') }}" alt="Logo Tether Brew" style="height: 60px; width: auto; display: block; margin: 0 auto 10px auto;">
        <h2 style="color: #22c55e; font-size: 1.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0;">Tether Brew</h2>
        <p style="margin: 5px 0 0; color: #64748b; font-size: 10pt;">Laporan Uang Makan Rider — Dicetak: {{ now()->translatedFormat('d F Y') }}</p>
    </div>

    {{-- Print Only Summary --}}
    <div class="print-summary-row" style="display: none; margin-bottom: 20px;">
        <div style="flex: 1; padding: 15px; text-align: center;">
            <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Total Estimasi Max</div>
            <div style="font-size: 14pt; font-weight: bold; color: #3b82f6;">Rp {{ number_format($totalEstimasiMax, 0, ',', '.') }}</div>
        </div>
        <div style="flex: 1; padding: 15px; text-align: center;">
            <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Belum Dibayar/Diambil</div>
            <div style="font-size: 14pt; font-weight: bold; color: #6366f1;">Rp {{ number_format($totalEarned, 0, ',', '.') }}</div>
        </div>
        <div style="flex: 1; padding: 15px; text-align: center;">
            <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Sudah Dibayar</div>
            <div style="font-size: 14pt; font-weight: bold; color: #22c55e;">Rp {{ number_format($totalSudahDiterima, 0, ',', '.') }}</div>
        </div>
        <div style="flex: 1; padding: 15px; text-align: center;">
            <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Sisa</div>
            <div style="font-size: 14pt; font-weight: bold; color: {{ $sisaBelumDibayar >= 0 ? '#eab308' : '#ef4444' }};">{{ $sisaBelumDibayar >= 0 ? '+' : '-' }} Rp {{ number_format(abs($sisaBelumDibayar), 0, ',', '.') }}</div>
        </div>
    </div>
<div x-data="{ open: false }" class="card mb-4 print-hide" style="margin-bottom:10px;background: linear-gradient(to right, rgba(59, 130, 246, 0.05), rgba(99, 102, 241, 0.05)); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px;">
    <div class="card-body" style="padding: 20px;">
        <h4 @click="open = !open" style="cursor: pointer; color: #1e293b; margin-top: 0; margin-bottom: 0; display: flex; align-items: center; justify-content: space-between; font-size: 1.1rem; user-select: none;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg> Cara Kerja Perhitungan Uang Makan
            </div>
            <svg :style="open ? 'transform: rotate(180deg); transition: transform 0.3s;' : 'transform: rotate(0deg); transition: transform 0.3s;'" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </h4>
        <div x-show="open" x-collapse>
            <ul style="margin: 12px 0 0 0; padding-left: 20px; font-size: 0.9rem; color: #475569; line-height: 1.6;">
                <li><strong>Total Estimasi Max:</strong> Target maksimal uang makan bulan ini (Target Cup Ã— Uang Makan per Cup).</li>
                <li><strong>Total Uang Makan Belum Dibayar/Diambil:</strong> Hak uang makan berdasarkan <strong style="color:#1e293b;">cup terjual sebenarnya bulan ini</strong>.</li>
                <li><strong>Sudah Dibayar:</strong> Total kasbon uang makan yang telah dicairkan bulan ini.</li>
                <li><strong>Sisa:</strong> Selisih antara Hak (Belum Dibayar/Diambil) dengan Kasbon yang sudah ditarik. Jika minus, artinya kasbon uang makan melebihi hak penjualan berjalan.</li>
                <li><strong style="color:#ef4444;">Note:</strong> Minus uang makan bulan lalu <strong style="color:#ef4444;">tidak otomatis memotong</strong> uang makan bulan berjalan, melainkan menjadi <strong>Outstanding Minus</strong> pada saat proses penggajian bulanan (Payroll).</li>
            </ul>
        </div>
    </div>
</div>

<div class="stats-grid print-hide">
    {{-- Estimasi Max (Total Target Semua) --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #3b82f6; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #3b82f6;">Rp {{ number_format($totalEstimasiMax, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Estimasi Max</div>
    </div>

    {{-- Total Uang Makan Earned (Aktual yang diperoleh) --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #6366f1; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #6366f1;">Rp {{ number_format($totalEarned, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Uang Makan Belum Dibayar/Diambil</div>
    </div>
    
    {{-- Sudah Dibayar --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #22c55e; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #22c55e;">Rp {{ number_format($totalSudahDiterima, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Sudah Dibayar</div>
    </div>

    {{-- Sisa Belum Dibayar --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: {{ $sisaBelumDibayar >= 0 ? '#eab308' : '#ef4444' }}; z-index: -1;">
            @if($sisaBelumDibayar >= 0)
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
            @else
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            @endif
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: {{ $sisaBelumDibayar >= 0 ? '#eab308' : '#ef4444' }};">
            {{ $sisaBelumDibayar >= 0 ? '+' : '-' }} Rp {{ number_format(abs($sisaBelumDibayar), 0, ',', '.') }}
        </div>
        <div class="stat-label" style="position: relative; z-index: 2;">Sisa</div>
    </div>
</div>

<div class="card">
    <div class="card-header print-hide" style="padding-bottom: 0;">
        <form method="GET" action="{{ route('admin.rider_finances.uang_makan') }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 20px;">
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
                <a href="{{ route('admin.rider_finances.uang_makan') }}" class="btn btn-secondary" style="height: 42px; display: flex; align-items: center;">Reset</a>
                <button type="button" onclick="openFinanceModal()" class="btn btn-primary flex-center" style="height: 42px; gap:0.5rem; white-space: nowrap;">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg> Input Uang Makan
                </button>
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
                        <th>Cup Terjual</th>
                        <th style="text-align: right;">Jumlah (Rp)</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($finances as $finance)
                        <tr class="uangmakan-row">
                            <td>{{ $finance->date->format('d/m/Y') }}</td>
                            <td>{{ $finance->rider->name }}</td>
                            <td><span class="badge badge-success">{{ $finance->reference_cups ?? 0 }} Cup</span></td>
                            <td style="text-align: right; font-weight: 600; color: #22c55e;">Rp {{ number_format($finance->amount, 0, ',', '.') }}</td>
                            <td>{{ $finance->notes ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.rider_finances.destroy', $finance->id) }}" data-confirm="Apakah Anda yakin ingin menghapus data ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm flex-center">
                                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">Belum ada riwayat uang makan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Controls --}}
        @if($finances->hasPages())
        <div class="pagination-container print-hide" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px 20px; border-top: 1px solid #e2e8f0;">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
                Menampilkan <span>{{ $finances->firstItem() }} - {{ $finances->lastItem() }}</span> dari {{ $finances->total() }} data
            </div>
            <div style="display: flex; gap: 5px;">
                @if($finances->onFirstPage())
                    <button class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; opacity: 0.5;" disabled>Sebelumnya</button>
                @else
                    <a href="{{ $finances->previousPageUrl() }}" class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; display: inline-flex; align-items: center;">Sebelumnya</a>
                @endif

                <div style="display: flex; gap: 5px;">
                    @php
                        $startPage = max(1, $finances->currentPage() - 2);
                        $endPage = min($finances->lastPage(), $startPage + 4);
                        if ($endPage - $startPage < 4) {
                            $startPage = max(1, $endPage - 4);
                        }
                    @endphp
                    @for($i = $startPage; $i <= $endPage; $i++)
                        @if($i == $finances->currentPage())
                            <button class="btn btn-sm" style="background: #8b5c2a; color: white; border: 1px solid #8b5c2a;" disabled>{{ $i }}</button>
                        @else
                            <a href="{{ $finances->url($i) }}" class="btn btn-sm" style="background: white; color: #475569; border: 1px solid #cbd5e1; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; min-width: 32px;">{{ $i }}</a>
                        @endif
                    @endfor
                </div>

                @if($finances->hasMorePages())
                    <a href="{{ $finances->nextPageUrl() }}" class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; display: inline-flex; align-items: center;">Selanjutnya</a>
                @else
                    <button class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; opacity: 0.5;" disabled>Selanjutnya</button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
</div> {{-- End #print-area --}}

<!-- Modal Input Uang Makan -->
<div id="financeModal" class="modal-overlay-animate" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card modal-content-animate" style="width: 100%; max-width: 600px; margin: 20px; box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <h3 class="card-title">Input Uang Makan Rider</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.rider_finances.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="uang_makan">
                
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Rider</label>
                        <select name="rider_id" id="rider_id" class="form-input" required>
                            <option value="">Pilih Rider...</option>
                            @foreach($riders as $rider)
                                <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                            @endforeach
                        </select>
                        @error('rider_id') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Tanggal</label>
                        <div class="flatpickr-input-container">
                            <input type="text" name="date" id="date" class="form-input" value="{{ old('date', date('Y-m-d')) }}" required placeholder="Pilih tanggal...">
                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        @error('date') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Cup Terjual</label>
                        <input type="number" name="reference_cups" id="reference_cups" class="form-input" value="{{ old('reference_cups') }}" readonly placeholder="Otomatis dari data penjualan">
                        <small style="color: var(--text-secondary);">Otomatis terisi jika ada data penjualan di tanggal tersebut.</small>
                        @error('reference_cups') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Jumlah Uang (Rp)</label>
                        <input type="number" name="amount" class="form-input" min="0" value="{{ old('amount') }}" required placeholder="0">
                        @error('amount') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 15px; margin-bottom: 1.5rem;">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" class="form-input" value="{{ old('notes') }}" placeholder="Opsional...">
                    @error('notes') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeFinanceModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Uang Makan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        @page { margin: 15mm; }
        body { font-size: 11pt; }
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 10px; box-sizing: border-box; }

        .print-title { display: block !important; text-align: center; padding-top: 20px; padding-bottom: 15px; border-bottom: 2px dashed #cbd5e1; margin-bottom: 20px !important; }
        .print-title h2 { margin: 0; color: #22c55e; font-size: 16pt; }
        .print-title p { margin: 5px 0 0 0; color: #64748b; font-size: 10pt; }

        .print-summary-row { display: flex !important; gap: 15px !important; }
        .print-summary-row > div { border: 1px solid #cbd5e1 !important; border-radius: 8px !important; background: white !important; }

        .topbar, .sidebar, .btn, .print-hide, .modal-overlay-animate, #financeModal, form[method="POST"] { display: none !important; }

        .card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; break-inside: avoid; margin-bottom: 20px !important; border-radius: 8px !important; }
        .card-header { background: transparent !important; border-bottom: 1px solid #cbd5e1 !important; padding: 12px 16px !important; }
        .card-header.print-hide { display: none !important; }
        .card-body { padding: 15px 16px !important; }

        table { width: 100% !important; border-collapse: collapse !important; }
        .uangmakan-row { display: table-row !important; }
        th { background-color: #f8fafc !important; color: #1e293b !important; border-bottom: 2px solid #cbd5e1 !important; padding: 10px 8px !important; font-weight: bold !important; font-size: 9.5pt !important; }
        td { border-bottom: 1px solid #e2e8f0 !important; padding: 8px !important; font-size: 10pt !important; }

        /* Hide action column */
        th:last-child, td:last-child { display: none !important; }
    }

    /* Card animated and absolute bg icon effects (matching owner dashboard) */
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

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-overlay-animate {
        animation: fadeIn 0.3s ease-out forwards;
    }

    .modal-content-animate {
        animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true,
            disableMobile: "true"
        });

        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true,
            disableMobile: "true"
        });

        const riderSelect = document.getElementById('rider_id');
        const dateInput = document.getElementById('date');
        const cupsInput = document.getElementById('reference_cups');

        function fetchCups() {
            const riderId = riderSelect.value;
            const date = dateInput.value;

            if (riderId && date) {
                fetch(`{{ route('admin.api.rider_cups') }}?rider_id=${riderId}&date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        cupsInput.value = data.cups;
                    })
                    .catch(error => {
                        console.error('Error fetching cups:', error);
                        cupsInput.value = '';
                    });
            } else {
                cupsInput.value = '';
            }
        }

        riderSelect.addEventListener('change', fetchCups);
        dateInput.addEventListener('change', fetchCups);
        
        @if($errors->any() && old('type') == 'uang_makan')
            openFinanceModal();
        @endif
    });

    function openFinanceModal() {
        document.getElementById('financeModal').style.display = 'flex';
    }

    function closeFinanceModal() {
        document.getElementById('financeModal').style.display = 'none';
    }

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('financeModal');
        if (event.target == modal) {
            closeFinanceModal();
        }
    });
</script>
@endpush
