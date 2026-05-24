@extends('layouts.app')

@section('title', 'Kasbon & Uang Makan Rider')

@section('content')


<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Riwayat Kasbon & Uang Makan</h3>
        <button onclick="openFinanceModal()" class="btn btn-primary btn-sm flex-center" style="gap: 0.5rem;">
            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
            </svg> Tambah Data
        </button>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Rider</th>
                        <th>Tipe</th>
                        <th style="text-align: right;">Jumlah (Rp)</th>
                        <th>Cup Terjual</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($finances as $finance)
                        <tr>
                            <td>{{ $finance->date->format('d/m/Y') }}</td>
                            <td>{{ $finance->rider->name }}</td>
                            <td>
                                @if($finance->type === 'kasbon')
                                    <span style="color: #ef4444; font-weight: 600; background: rgba(239, 68, 68, 0.1); padding: 2px 8px; border-radius: 4px;">Kasbon</span>
                                @else
                                    <span style="color: #22c55e; font-weight: 600; background: rgba(34, 197, 94, 0.1); padding: 2px 8px; border-radius: 4px;">Uang Makan</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($finance->amount, 0, ',', '.') }}</td>
                            <td>{{ $finance->reference_cups ?? '-' }}</td>
                            <td>{{ $finance->notes ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.rider_finances.destroy', $finance->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="color: var(--accent-red); background: rgba(239, 68, 68, 0.1); border-radius: 8px; border: none; padding: 5px 10px;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Modal Input Kasbon & Uang Makan -->
<div id="financeModal" class="modal-overlay-animate" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card modal-content-animate" style="width: 100%; max-width: 600px; margin: 20px; box-shadow: var(--shadow-lg);">
        <div class="card-header">
            <h3 class="card-title">Input Kasbon & Uang Makan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.rider_finances.store') }}" method="POST">
                @csrf
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Rider</label>
                        <select name="rider_id" id="rider_id" class="form-input" required>
                            <option value="">Pilih Rider...</option>
                            @foreach($riders as $rider)
                                <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Tanggal</label>
                        <div class="flatpickr-input-container">
                            <input type="text" name="date" id="date" class="form-input" value="{{ date('Y-m-d') }}" required placeholder="Pilih tanggal...">
                            <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                    </div>

                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-input" required>
                            <option value="kasbon">Kasbon</option>
                            <option value="uang_makan">Uang Makan</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Cup Terjual</label>
                        <input type="number" name="reference_cups" id="reference_cups" class="form-input" readonly placeholder="Otomatis dari data penjualan">
                        <small style="color: var(--text-secondary);">Otomatis terisi jika ada data penjualan di tanggal tersebut.</small>
                    </div>
                    
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label class="form-label">Jumlah Uang (Rp)</label>
                        <input type="number" name="amount" class="form-input" min="0" required placeholder="0">
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 15px;">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" class="form-input" placeholder="Opsional...">
                </div>

                <div style="margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeFinanceModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
