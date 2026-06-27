@extends('layouts.app')

@section('title', 'Jurnal Umum')

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
            <p style="margin: 5px 0 0; color: #64748b; font-size: 10pt;">Laporan Jurnal Umum — Dicetak: {{ now()->translatedFormat('d F Y') }}</p>
        </div>

        {{-- Print Only Summary --}}
        <div class="journal-summary-row" style="display: none; margin-bottom: 20px;">
            <div style="flex: 1; padding: 15px; text-align: center;">
                <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Total Pemasukan
                    (Debit)</div>
                <div style="font-size: 14pt; font-weight: bold; color: #22c55e;">Rp
                    {{ number_format($totalDebit, 0, ',', '.') }}
                </div>
            </div>
            <div style="flex: 1; padding: 15px; text-align: center;">
                <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Total Pengeluaran
                    (Kredit)</div>
                <div style="font-size: 14pt; font-weight: bold; color: #ef4444;">Rp
                    {{ number_format($totalCredit, 0, ',', '.') }}
                </div>
            </div>
            <div style="flex: 1; padding: 15px; text-align: center;">
                <div style="font-size: 10pt; color: #64748b; margin-bottom: 5px; text-transform: uppercase;">Saldo Saat Ini
                </div>
                <div style="font-size: 14pt; font-weight: bold; color: #1e293b;">Rp {{ number_format($balance, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="stats-grid print-hide mb-4">
        {{-- Card Debit --}}
        <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
            <div class="stat-icon-bg"
                style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #22c55e; z-index: -1;">
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <polyline points="19 12 12 19 5 12"></polyline>
                </svg>
            </div>
            <div class="stat-value" style="position: relative; z-index: 2; color: #22c55e;">Rp
                {{ number_format($totalDebit, 0, ',', '.') }}
            </div>
            <div class="stat-label" style="position: relative; z-index: 2;">Total Pemasukan (Debit)</div>
            </div>

        {{-- Card Kredit --}}
        <div class="stat-card stat-card-animated" style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px;">
            <div class="stat-icon-bg"
                style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.15; color: #ef4444; z-index: -1;">
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="19" x2="12" y2="5"></line>
                    <polyline points="5 12 12 5 19 12"></polyline>
                </svg>
            </div>
            <div class="stat-value" style="position: relative; z-index: 2; color: #ef4444;">Rp
                {{ number_format($totalCredit, 0, ',', '.') }}
            </div>
            <div class="stat-label" style="position: relative; z-index: 2;">Total Pengeluaran (Kredit)</div>
            </div>

        {{-- Card Saldo --}}
        <div class="stat-card stat-card-animated"
            style="position: relative; overflow: hidden; z-index: 1; padding-top: 40px; background: var(--gradient-gold); color: white;">
            <div class="stat-icon-bg"
                style="position: absolute; top: -25px; left: -20px; width: 130px; height: 130px; opacity: 0.25; color: white; z-index: -1;">
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="M2 10h20"></path>
                    <path d="M16 14h2"></path>
                </svg>
            </div>
            <div class="stat-value" style="position: relative; z-index: 2; color: white;">Rp
                {{ number_format($balance, 0, ',', '.') }}
            </div>
            <div class="stat-label" style="position: relative; z-index: 2; opacity: 0.9; color:white">Saldo Saat Ini</div>
            </div>
            </div>

    <div class="card">
        <div class="card-header print-hide" style="padding-bottom: 0;">
            <form method="GET" action="{{ route('admin.journals.index') }}"
                style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 20px;">
                <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                    <label class="form-label">Mulai Tanggal</label>
                    <div class="flatpickr-input-container">
                        <input type="text" name="start_date" id="start_date" class="form-input datepicker" value="{{ request('start_date') }}"
                            placeholder="Pilih tanggal...">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        </div>
                        </div>
                        <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                            <label class="form-label">Sampai Tanggal</label>
                            <div class="flatpickr-input-container">
                        <input type="text" name="end_date" id="end_date" class="form-input datepicker" value="{{ request('end_date') }}"
                            placeholder="Pilih tanggal...">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        </div>
                        </div>
                        <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-input">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div class="form-group" style="display: flex; gap: 8px; margin-bottom: 0;">
                                <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
                    <a href="{{ route('admin.journals.index') }}" class="btn btn-secondary"
                        style="height: 42px; display: flex; align-items: center;">Reset</a>

                    @if(auth()->check() && auth()->user()->isOwner())
                    <button type="button" onclick="openDeleteAllModal()" class="btn btn-secondary flex-center"
                        style="height: 42px; gap:0.5rem; white-space: nowrap; background: #ef4444; color: white; border-color: #ef4444;">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg> Hapus Semua Data
                    </button>
                    @endif
                    
                    <button type="button" onclick="openImportModal()" class="btn btn-secondary flex-center"
                        style="height: 42px; gap:0.5rem; white-space: nowrap; background: #10b981; color: white; border-color: #10b981;">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg> Import Excel
                    </button>

                    <button type="button" onclick="openJournalModal()" class="btn btn-primary flex-center"
                        style="height: 42px; gap:0.5rem; white-space: nowrap;">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg> Input Jurnal
                    </button>
                    </div>
                    </form>
                    </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const commonConfig = {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "j F Y",
                        allowInput: true,
                        disableMobile: "true"
                    };

                    flatpickr("#start_date", {
                        ...commonConfig,
                        onChange: function (selectedDates, dateStr, instance) {
                            endPicker.set('minDate', dateStr);
                        }
                    });

                    const endPicker = flatpickr("#end_date", {
                        ...commonConfig,
                        minDate: document.getElementById('start_date').value || null
                    });

                    flatpickr("#modal_date", commonConfig);
                });

                function openJournalModal() {
                    document.getElementById('journalModal').style.display = 'flex';
                }

                function closeJournalModal() {
                    document.getElementById('journalModal').style.display = 'none';
                }

                window.addEventListener('click', function (event) {
                    const modal = document.getElementById('journalModal');
                    const importModal = document.getElementById('importModal');
                    const deleteAllModal = document.getElementById('deleteAllModal');
                    if (event.target == modal) {
                        closeJournalModal();
                    }
                    if (event.target == importModal) {
                        closeImportModal();
                    }
                    if (deleteAllModal && event.target == deleteAllModal) {
                        closeDeleteAllModal();
                    }
                });
                
                function openImportModal() {
                    document.getElementById('importModal').style.display = 'flex';
                }

                function closeImportModal() {
                    document.getElementById('importModal').style.display = 'none';
                }
                
                function openDeleteAllModal() {
                    const modal = document.getElementById('deleteAllModal');
                    if(modal) {
                        modal.style.display = 'flex';
                        document.getElementById('deleteAllInput').value = '';
                        document.getElementById('deleteAllSubmitBtn').disabled = true;
                    }
                }

                function closeDeleteAllModal() {
                    const modal = document.getElementById('deleteAllModal');
                    if(modal) modal.style.display = 'none';
                }

                function checkDeleteAllInput() {
                    const input = document.getElementById('deleteAllInput').value;
                    const btn = document.getElementById('deleteAllSubmitBtn');
                    if (input === 'HAPUS SEMUA JURNAL') {
                        btn.disabled = false;
                    } else {
                        btn.disabled = true;
                    }
                }

                @if($errors->any())
                    openJournalModal();
                @endif


            </script>
        @endpush
        <div class="card-body">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Tipe</th>
                            <th style="text-align: right;">Jumlah (Rp)</th>
                            <th>Diinput Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="journal-table-body">
                        @forelse($journals as $journal)
                            <tr class="journal-row">
                                <td>{{ $journal->date->format('d/m/Y') }}</td>
                                <td>
                                    @if($journal->category)
                                        <span
                                            style="display: inline-block; padding: 2px 8px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">{{ $journal->category->name }}</span>
                                    @else
                                        <span style="color: var(--text-muted); font-style: italic;">-</span>
                                    @endif
                                </td>
                                <td>{{ $journal->description }}</td>
                                <td>
                                    @if($journal->type === 'debit')
                                        <span style="color: #22c55e; font-weight: 600;">Debit (Masuk)</span>
                                    @else
                                        <span style="color: #ef4444; font-weight: 600;">Kredit (Keluar)</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">{{ number_format($journal->amount, 0, ',', '.') }}</td>
                                <td>{{ $journal->creator->name }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.journals.destroy', $journal->id) }}"
                                        data-confirm="Apakah Anda yakin ingin menghapus data ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm flex-center">
                                            <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-data-row">
                                <td colspan="7" style="text-align: center;">Belum ada data jurnal.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Controls --}}
            @if($journals->hasPages())
                <div class="pagination-container print-hide"
                    style="margin-top: 20px; padding: 15px 20px; border-top: 1px solid #e2e8f0;">
                    {{ $journals->links() }}
                </div>
            @endif

            </div>
            </div>
            </div>
            </div> {{-- End #print-area --}}

    <!-- Modal Input Jurnal -->
    <div id="journalModal" class="modal-overlay-animate"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card modal-content-animate"
            style="width: 100%; max-width: 500px; margin: 20px; box-shadow: var(--shadow-lg);">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Jurnal</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.journals.store') }}" method="POST">
                    @csrf
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Tanggal</label>
                        <div class="flatpickr-input-container">
                            <input type="text" name="date" id="modal_date" class="form-input" value="{{ date('Y-m-d') }}" required
                                placeholder="Pilih tanggal...">
                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        @error('date') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Tipe Transaksi</label>
                        <select name="type" class="form-input" required>
                            <option value="debit" {{ old('type') == 'debit' ? 'selected' : '' }}>Debit (Pemasukan)</option>
                            <option value="credit" {{ old('type') == 'credit' ? 'selected' : '' }}>Kredit (Pengeluaran)</option>
                        </select>
                        @error('type') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Kategori</label>
                        <select name="journal_category_id" class="form-input">
                            <option value="">Pilih Kategori (Opsional)...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('journal_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('journal_category_id') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Keterangan / Catatan</label>
                        <input type="text" name="description" class="form-input" value="{{ old('description') }}" placeholder="Contoh: Beli gula, Pendapatan lain-lain..." required>
                        @error('description') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Jumlah Uang (Rp)</label>
                        <input type="number" name="amount" class="form-input" value="{{ old('amount') }}" placeholder="0" min="0" required>
                        @error('amount') <div class="form-error" style="color: #ef4444; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="closeJournalModal()" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Jurnal</button>
                    </div>
                    </form>
                    </div>
                    </div>
                    </div>

    <!-- Modal Import Excel -->
    <div id="importModal" class="modal-overlay-animate"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card modal-content-animate"
            style="width: 100%; max-width: 500px; margin: 20px; box-shadow: var(--shadow-lg);">
            <div class="card-header">
                <h3 class="card-title">Import Data Jurnal via Excel</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 20px;">
                    <p style="font-size: 0.9rem; color: #475569; margin-bottom: 10px;">
                        Gunakan file template Excel berikut agar format data sesuai dengan sistem.
                    </p>
                    <a href="{{ route('admin.journals.downloadTemplate') }}" class="btn btn-secondary" style="font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg> Download Template (.csv)
                    </a>
                </div>

                <form action="{{ route('admin.journals.import') }}" method="POST" enctype="multipart/form-data">
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

    @if(auth()->check() && auth()->user()->isOwner())
    <!-- Modal Delete All -->
    <div id="deleteAllModal" class="modal-overlay-animate"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card modal-content-animate"
            style="width: 100%; max-width: 500px; margin: 20px; box-shadow: var(--shadow-lg);">
            <div class="card-header">
                <h3 class="card-title" style="color: #ef4444;">⚠️ Hapus Seluruh Data Jurnal</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 20px;">
                    <p style="font-size: 0.95rem; color: #475569; margin-bottom: 10px;">
                        Anda akan menghapus <strong>seluruh data jurnal</strong> pada cabang ini. Tindakan ini sangat destruktif dan tidak dapat dibatalkan.
                    </p>
                    <p style="font-size: 0.95rem; color: #475569; margin-bottom: 15px;">
                        Untuk melanjutkan, silakan ketik kalimat berikut ke dalam kotak di bawah ini:<br>
                        <strong style="color: #ef4444; user-select: all; padding: 2px 4px; background: #fee2e2; border-radius: 4px;">HAPUS SEMUA JURNAL</strong>
                    </p>
                </div>

                <form action="{{ route('admin.journals.destroyAll') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <input type="text" id="deleteAllInput" class="form-input" placeholder="Ketik HAPUS SEMUA JURNAL disini..." autocomplete="off" onkeyup="checkDeleteAllInput()">
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" onclick="closeDeleteAllModal()" class="btn btn-secondary">Batal</button>
                        <button type="submit" id="deleteAllSubmitBtn" class="btn btn-primary" style="background: #ef4444; border-color: #ef4444;" disabled>Hapus Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('styles')
    <style>
        @media print {
            @page {
                margin: 15mm;
            }

            body {
                font-size: 11pt;
            }

            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 10px;
                box-sizing: border-box;
            }

            .print-title {
                display: block !important;
                text-align: center;
                padding-top: 20px;
                padding-bottom: 15px;
                border-bottom: 2px dashed #cbd5e1;
                margin-bottom: 20px !important;
            }

            .print-title h2 {
                margin: 0;
                color: #22c55e;
                font-size: 16pt;
            }

            .print-title p {
                margin: 5px 0 0 0;
                color: #64748b;
                font-size: 10pt;
            }

            .topbar,
            .sidebar,
            .btn,
            .print-hide,
            #journalModal,
            form[method="POST"],
            .modal-overlay-animate {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                break-inside: avoid;
                margin-bottom: 20px !important;
                border-radius: 8px !important;
            }

            .card-header {
                background: transparent !important;
                border-bottom: 1px solid #cbd5e1 !important;
                padding: 12px 16px !important;
            }

            .card-header.print-hide {
                display: none !important;
            }

            .card-body {
                padding: 15px 16px !important;
            }

            /* Summary cards styling for print */
            .journal-summary-row { display: flex !important; gap: 15px !important; }
            .journal-summary-row > div { border: 1px solid #cbd5e1 !important; border-radius: 8px !important; background: white !important; color: #1e293b !important; }
            .journal-summary-row > div:last-child { background: #f8f4ef !important; color: #1e293b !important; }

            /* Table print styles */
            table { width: 100% !important; border-collapse: collapse !important; }
            /* FORCE all rows to display when printing regardless of pagination */
            .journal-row { display: table-row !important; }
            th { background-color: #f8fafc !important; color: #1e293b !important; border-bottom: 2px solid #cbd5e1 !important; padding: 10px 8px !important; font-weight: bold !important; font-size: 9.5pt !important; }
            td { border-bottom: 1px solid #e2e8f0 !important; padding: 8px !important; font-size: 10pt !important; }
            tfoot td { border-bottom: none !important; border-top: 2px solid #cbd5e1 !important; }

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
