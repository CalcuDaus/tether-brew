@extends('layouts.app')

@section('title', 'Tambah Stok Produksi')

@section('content')
<div class="card mb-4" style="max-width: 1400px; margin: 0 auto; background: var(--bg-card);">
    <div class="card-header" style="text-align: center; border-bottom: 2px solid var(--border-color); padding: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0; font-size: 1.5rem; letter-spacing: 1px;">TAMBAH STOK PRODUKSI</h2>
        <div style="font-size: 0.95rem; color: var(--text-muted); margin-top: 8px;">
            Tanggal: <strong>{{ \Carbon\Carbon::parse($production->date)->translatedFormat('d F Y') }}</strong>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.productions.store_additional', $production->id) }}" method="POST" id="production-form">
            @csrf

            <div style="background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 8px; padding: 15px; margin-bottom: 25px; color: #166534; font-size: 0.9rem;">
                <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -0.2em; margin-right: 5px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Masukkan <strong>angka tambahan</strong> produksi baru. Sistem akan otomatis menjumlahkan angka yang Anda masukkan dengan stok produksi yang sudah ada sebelumnya.
            </div>

            <!-- Matrix Table (Horizontal) -->
            <div class="table-container no-search" style="margin-bottom: 40px; border: 1px solid var(--border-color); border-radius: 12px; overflow-x: auto;">
                <table class="table" style="min-width: 1400px; margin: 0; white-space: nowrap;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="padding: 15px; font-weight: 600; border-right: 1px solid var(--border-color); position: sticky; left: 0; background: var(--bg-card); z-index: 10;">Keterangan</th>
                            @foreach($products as $product)
                                <th style="padding: 15px; font-weight: 600; text-align: center; border-right: 1px solid var(--border-color); min-width: 100px;">
                                    {{ $product->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 15px; font-weight: 600; border-right: 1px solid var(--border-color); position: sticky; left: 0; background: var(--bg-card); z-index: 10;">
                                Stok Sebelumnya
                            </td>
                            @foreach($products as $product)
                                @php
                                    $existingItem = $production->items->where('product_id', $product->id)->first();
                                    $existingQty = $existingItem ? $existingItem->quantity_produced : 0;
                                @endphp
                                <td style="padding: 8px; text-align: center; border-right: 1px solid var(--border-color); background: rgba(0,0,0,0.02); color: var(--text-muted); font-weight: 600;">
                                    {{ $existingQty }}
                                </td>
                            @endforeach
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 15px; font-weight: 600; border-right: 1px solid var(--border-color); position: sticky; left: 0; background: var(--bg-card); z-index: 10;">
                                Tambahan Baru <span style="color: #ef4444;">*</span>
                            </td>
                            @foreach($products as $product)
                                <td style="padding: 8px; text-align: center; border-right: 1px solid var(--border-color);">
                                    <input type="number" 
                                           name="items[{{ $product->id }}][quantity_produced]" 
                                           class="form-input production-input"
                                           data-product-id="{{ $product->id }}"
                                           min="0" 
                                           style="width: 80px; text-align: center; margin: 0 auto; padding: 6px; font-size: 0.95rem;">
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div style="margin-bottom: 30px; padding: 25px; background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: 12px;">
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1; min-width: 200px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; color: #22c55e;">Total Tambahan Item</label>
                        <input type="number" id="total-produksi" class="form-input" readonly
                               style="height: 44px; font-size: 1.1rem; font-weight: 700; background: rgba(34,197,94,0.08); color: #22c55e; cursor: not-allowed;">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <a href="{{ route('admin.productions.index') }}" class="btn btn-secondary" style="height: 48px; padding: 0 30px; font-size: 1rem; font-weight: 600; display: flex; align-items: center;">Batal</a>
                <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 40px; font-size: 1rem; font-weight: 600; background: #22c55e; border-color: #22c55e;">Simpan Tambahan Stok</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('production-form');

    // Prevent scroll-wheel changing number input values
    form.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('wheel', (e) => e.preventDefault());
        input.addEventListener('focus', function() {
            if(this.value === '0' || this.value === '') this.value = '';
        });
        input.addEventListener('blur', function() {
            if(this.value === '') this.value = '';
        });
    });

    function recalcAll() {
        let totalProduksi = 0;

        document.querySelectorAll('.production-input').forEach(input => {
            const qty = parseInt(input.value) || 0;
            totalProduksi += qty;
        });

        const totalProduksiInput = document.getElementById('total-produksi');
        if (totalProduksiInput) totalProduksiInput.value = totalProduksi;
    }

    form.querySelectorAll('.production-input').forEach(input => {
        input.addEventListener('input', recalcAll);
    });
});
</script>
@endpush
