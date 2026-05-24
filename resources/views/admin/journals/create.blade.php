@extends('layouts.app')

@section('title', 'Input Jurnal Manual')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Tambah Data Jurnal</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.journals.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Tanggal</label>
                <div class="flatpickr-input-container">
                    <input type="text" name="date" id="date" class="form-input datepicker" value="{{ date('Y-m-d') }}" required placeholder="Pilih tanggal...">
                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>

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
    });
</script>
@endpush

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Tipe Transaksi</label>
                <select name="type" class="form-input" required>
                    <option value="debit">Debit (Pemasukan)</option>
                    <option value="credit">Kredit (Pengeluaran)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Kategori</label>
                <select name="journal_category_id" class="form-input">
                    <option value="">Pilih Kategori (Opsional)...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Keterangan / Catatan</label>
                <input type="text" name="description" class="form-input" placeholder="Contoh: Beli gula, Pendapatan lain-lain..." required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Jumlah Uang (Rp)</label>
                <input type="number" name="amount" class="form-input" placeholder="0" min="0" required>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('admin.journals.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Jurnal</button>
            </div>
        </form>
    </div>
</div>
@endsection
