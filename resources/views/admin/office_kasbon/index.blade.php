@extends('layouts.app')

@section('title', 'Kasbon Office')

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
        <p style="margin: 5px 0 0; color: #64748b; font-size: 10pt;">Laporan Kasbon Office — Dicetak: {{ now()->translatedFormat('d F Y') }}</p>
    </div>

    {{-- Print Only Summary --}}
    <div class="print-summary-row" style="display: none; margin-bottom: 20px;">
        <div style="flex: 1; padding: 15px; text-align: center;">
            <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Total Kasbon</div>
            <div style="font-size: 14pt; font-weight: bold; color: #ef4444;">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</div>
        </div>
        <div style="flex: 1; padding: 15px; text-align: center;">
            <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Total Terbayar</div>
            <div style="font-size: 14pt; font-weight: bold; color: #10b981;">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
        </div>
        <div style="flex: 1; padding: 15px; text-align: center;">
            <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Total Sisa</div>
            <div style="font-size: 14pt; font-weight: bold; color: #f59e0b;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
        </div>
    </div>
<div class="stats-grid print-hide mb-4">
    {{-- Card Total Kasbon --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #ef4444; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="19" x2="12" y2="5"></line>
                <polyline points="5 12 12 5 19 12"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #ef4444;">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Kasbon</div>
    </div>
    
    {{-- Card Total Terbayar --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #10b981; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <polyline points="19 12 12 19 5 12"></polyline>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #10b981;">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Terbayar</div>
    </div>

    {{-- Card Total Sisa --}}
    <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
        <div class="stat-icon-bg" style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #f59e0b; z-index: -1;">
            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2" ry="2"></rect><line x1="12" y1="12" x2="12" y2="12"></line>
            </svg>
        </div>
        <div class="stat-value" style="position: relative; z-index: 2; color: #f59e0b;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
        <div class="stat-label" style="position: relative; z-index: 2;">Total Sisa</div>
    </div>
</div>

<div class="card">
    <div class="card-header print-hide" style="padding-bottom: 0;">
        <form method="GET" action="{{ route('admin.office_kasbon.index') }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 20px;">
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
                <label class="form-label">Pencarian</label>
                <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Keterangan kasbon...">
            </div>
            <div class="form-group" style="display: flex; gap: 8px; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
                <a href="{{ route('admin.office_kasbon.index') }}" class="btn btn-secondary" style="height: 42px; display: flex; align-items: center;">Reset</a>
                
                <button type="button" onclick="openImportModal()" class="btn btn-secondary flex-center"
                    style="height: 42px; gap:0.5rem; white-space: nowrap; background: #10b981; color: white; border-color: #10b981;">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg> Import Excel
                </button>

                <button type="button" onclick="openFinanceModal()" class="btn btn-primary flex-center" style="height: 42px; gap:0.5rem; white-space: nowrap;">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg> Input Kasbon
                </button>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-container no-search">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th style="text-align: right;">Total Kasbon</th>
                        <th style="text-align: right;">Sisa Tagihan</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($finances as $finance)
                        <tr class="kasbon-row">
                            <td>{{ $finance->date->format('d/m/Y') }}</td>
                            <td>{{ $finance->name }}</td>
                            <td style="text-align: right; font-weight: 600; color: #ef4444;">Rp {{ number_format($finance->amount, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 600; color: #f59e0b;">Rp {{ number_format($finance->amount - $finance->paid_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($finance->status === 'paid')
                                    <span class="badge badge-cash">LUNAS</span>
                                @elseif($finance->status === 'partial')
                                    <span class="badge badge-transfer">SEBAGIAN</span>
                                @else
                                    <span class="badge badge-qris">BELUM LUNAS</span>
                                @endif
                            </td>
                            <td>
                                {{ $finance->notes ?? '-' }}
                                @if($finance->payments->count() > 0)
                                    <br><small class="text-md-muted">{{ $finance->payments->count() }}x pembayaran</small>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    @if($finance->status !== 'paid')
                                        <button type="button" class="btn btn-sm" style="color: var(--accent); background: rgba(59, 130, 246, 0.1); border-radius: 8px; border: none; padding: 5px 10px;" onclick="openPaymentModal({{ $finance->id }}, '{{ $finance->name }}', {{ $finance->amount - $finance->paid_amount }})">Bayar</button>
                                    @endif
                                    <form method="POST" action="{{ route('admin.office_kasbon.destroy', $finance->id) }}" data-confirm="Apakah Anda yakin ingin menghapus data kasbon ini? Semua history jurnal terkait kasbon ini akan terhapus juga.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm flex-center">
                                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px;">Belum ada riwayat kasbon.</td>
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

<!-- Modal Input Kasbon -->
<div id="financeModal" class="modal-overlay-animate" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card modal-content-animate" style="width: 100%; max-width: 500px; margin: 20px; box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <h3 class="card-title">Input Kasbon Office</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.office_kasbon.store') }}" method="POST">
                @csrf
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Keterangan / Nama Staff</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="Contoh: Admin Ucok, Beli air minum, dsb...">
                    @error('name') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Tanggal</label>
                    <div class="flatpickr-input-container">
                        <input type="text" name="date" id="date" class="form-input" value="{{ old('date', date('Y-m-d')) }}" required placeholder="Pilih tanggal...">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    @error('date') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Jumlah Uang (Rp)</label>
                    <input type="number" name="amount" class="form-input" min="1" value="{{ old('amount') }}" required placeholder="0">
                    @error('amount') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Catatan Tambahan</label>
                    <input type="text" name="notes" class="form-input" value="{{ old('notes') }}" placeholder="Opsional...">
                    @error('notes') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeFinanceModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kasbon</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Payment Kasbon -->
<div id="paymentModal" class="modal-overlay-animate" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card modal-content-animate" style="width: 100%; max-width: 500px; margin: 20px; box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <h3 class="card-title">Bayar Kasbon Office</h3>
        </div>
        <div class="card-body">
            <form id="paymentForm" action="" method="POST">
                @csrf
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Pembayaran Untuk</label>
                    <input type="text" id="payment_name" class="form-input" disabled>
                </div>
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Tanggal Bayar</label>
                    <div class="flatpickr-input-container">
                        <input type="text" name="date" id="payment_date" class="form-input" value="{{ old('date', date('Y-m-d')) }}" required placeholder="Pilih tanggal...">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Jumlah Bayar (Rp) - <small>Sisa: <span id="payment_sisa_text">0</span></small></label>
                    <input type="number" name="amount" id="payment_amount" class="form-input" min="1" required placeholder="0">
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" class="form-input" placeholder="Opsional...">
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closePaymentModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="importModal" class="modal-overlay-animate" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card modal-content-animate" style="width: 100%; max-width: 500px; margin: 20px; box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <h3 class="card-title">Import Kasbon Office via Excel</h3>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 20px;">
                <p style="font-size: 0.9rem; color: #475569; margin-bottom: 10px;">
                    Gunakan file template Excel berikut agar format data sesuai dengan sistem (Kolom NO, NAMA, TGL, JUMLAH KASBON).
                </p>
                <a href="{{ route('admin.office_kasbon.downloadTemplate') }}" class="btn btn-secondary" style="font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg> Download Template (.csv)
                </a>
            </div>

            <form action="{{ route('admin.office_kasbon.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Upload File (Excel/CSV)</label>
                    <input type="file" name="file" class="form-input" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                    <small style="color: #64748b; font-size: 0.8rem; margin-top: 5px; display: block;">Maksimal ukuran file: 10MB.</small>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeImportModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background: #10b981; border-color: #10b981;">Proses Import</button>
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

        .topbar, .sidebar, .btn, .print-hide, .modal-overlay-animate, #financeModal, #paymentModal, form[method="POST"] { display: none !important; }

        .card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; break-inside: avoid; margin-bottom: 20px !important; border-radius: 8px !important; }
        .card-header { background: transparent !important; border-bottom: 1px solid #cbd5e1 !important; padding: 12px 16px !important; }
        .card-header.print-hide { display: none !important; }
        .card-body { padding: 15px 16px !important; }

        table { width: 100% !important; border-collapse: collapse !important; }
        .kasbon-row { display: table-row !important; }
        th { background-color: #f8fafc !important; color: #1e293b !important; border-bottom: 2px solid #cbd5e1 !important; padding: 10px 8px !important; font-weight: bold !important; font-size: 9.5pt !important; }
        td { border-bottom: 1px solid #e2e8f0 !important; padding: 8px !important; font-size: 10pt !important; }

        /* Hide action column */
        th:last-child, td:last-child { display: none !important; }
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
    function openFinanceModal() {
        document.getElementById('financeModal').style.display = 'flex';
    }

    function closeFinanceModal() {
        document.getElementById('financeModal').style.display = 'none';
    }

    function openPaymentModal(id, name, sisa) {
        document.getElementById('paymentForm').action = '/admin/office-kasbon/' + id + '/payment';
        document.getElementById('payment_name').value = name;
        document.getElementById('payment_amount').max = sisa;
        document.getElementById('payment_amount').value = sisa; // Default isi semua sisa
        document.getElementById('payment_sisa_text').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(sisa);
        document.getElementById('paymentModal').style.display = 'flex';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }

    function openImportModal() {
        document.getElementById('importModal').style.display = 'flex';
    }

    function closeImportModal() {
        document.getElementById('importModal').style.display = 'none';
    }

    window.addEventListener('click', function(event) {
        const financeModal = document.getElementById('financeModal');
        const paymentModal = document.getElementById('paymentModal');
        const importModal = document.getElementById('importModal');
        
        if (event.target == financeModal) {
            closeFinanceModal();
        }
        if (event.target == paymentModal) {
            closePaymentModal();
        }
        if (event.target == importModal) {
            closeImportModal();
        }
    });

    // Initialize flatpickr when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j F Y",
                allowInput: true,
                disableMobile: "true"
            });
            
            flatpickr("#payment_date", {
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
        }
        
        @if($errors->any() && !old('payment_date'))
            openFinanceModal();
        @endif

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('financeModal');
            const modal2 = document.getElementById('paymentModal');
            if (event.target == modal) {
                closeFinanceModal();
            }
            if (event.target == modal2) {
                closePaymentModal();
            }
        });
    });
</script>
@endpush
