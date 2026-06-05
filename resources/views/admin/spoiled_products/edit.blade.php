@extends('layouts.app')

@section('title', 'Edit Produk Basi')

@section('content')
<div class="card mb-4" style="max-width: 1400px; margin: 0 auto; background: var(--bg-card);">
    <div class="card-header" style="text-align: center; border-bottom: 2px solid var(--border-color); padding: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0; font-size: 1.5rem; letter-spacing: 1px;">EDIT PRODUK BASI</h2>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.spoiled_products.update', $spoiledProduct->id) }}" method="POST" id="spoiled-form">
            @csrf
            @method('PUT')

            <!-- Date -->
            <div style="margin-bottom: 30px; max-width: 350px;">
                <label class="form-label" style="font-weight: 600; text-transform: uppercase;">TANGGAL</label>
                <div class="flatpickr-input-container">
                    <input type="text" name="date" id="date" class="form-input" value="{{ old('date', $spoiledProduct->date->format('Y-m-d')) }}" required placeholder="Pilih tanggal..." style="height: 48px; font-size: 1rem;">
                    <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>

            <!-- Matrix Table (Horizontal like Rider Sales) -->
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
                                Jumlah Basi
                            </td>
                            @foreach($products as $product)
                                @php
                                    $item = $spoiledProduct->items->where('product_id', $product->id)->first();
                                    $qty = $item ? $item->quantity : 0;
                                @endphp
                                <td style="padding: 8px; text-align: center; border-right: 1px solid var(--border-color);">
                                    <input type="number" 
                                           name="items[{{ $product->id }}][quantity]" 
                                           class="form-input spoiled-input"
                                           data-product-id="{{ $product->id }}"
                                           data-price="{{ $product->price }}"
                                           min="0" 
                                           value="{{ $qty }}"
                                           style="width: 80px; text-align: center; margin: 0 auto; padding: 6px; font-size: 0.95rem;">
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 2px solid var(--border-color); background: rgba(0,0,0,0.03);">
                            <td style="padding: 12px 15px; font-weight: 700; border-right: 1px solid var(--border-color); position: sticky; left: 0; background: var(--bg-card); z-index: 10;">
                                Kerugian (Rp)
                            </td>
                            @foreach($products as $product)
                                @php
                                    $item = $spoiledProduct->items->where('product_id', $product->id)->first();
                                    $qty = $item ? $item->quantity : 0;
                                    $loss = $qty * $product->price;
                                @endphp
                                <td style="padding: 12px 8px; text-align: center; border-right: 1px solid var(--border-color); font-weight: 700; color: #ef4444;">
                                    <span class="loss-cell" data-product-id="{{ $product->id }}" data-price="{{ $product->price }}">
                                        Rp {{ number_format($loss, 0, ',', '.') }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Summary -->
            <div style="margin-bottom: 30px; padding: 25px; background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: 12px;">
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1; min-width: 200px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; color: #ef4444;">Total Item Basi</label>
                        <input type="number" id="total-basi" class="form-input" readonly
                               value="{{ $spoiledProduct->items->sum('quantity') }}"
                               style="height: 44px; font-size: 1.1rem; font-weight: 700; background: rgba(239,68,68,0.08); color: #ef4444; cursor: not-allowed;">
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 200px; margin: 0;">
                        <label class="form-label" style="font-weight: 600; color: #ef4444;">Total Kerugian</label>
                        @php
                            $totalKerugian = 0;
                            foreach ($spoiledProduct->items as $item) {
                                $totalKerugian += $item->quantity * ($item->product->price ?? 0);
                            }
                        @endphp
                        <input type="text" id="total-kerugian" class="form-input" readonly
                               value="Rp {{ number_format($totalKerugian, 0, ',', '.') }}"
                               style="height: 44px; font-size: 1.1rem; font-weight: 700; background: rgba(239,68,68,0.08); color: #ef4444; cursor: not-allowed;">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <a href="{{ route('admin.spoiled_products.index') }}" class="btn btn-secondary" style="height: 48px; padding: 0 30px; font-size: 1rem; font-weight: 600; display: flex; align-items: center;">Batal</a>
                <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 40px; font-size: 1rem; font-weight: 600;">Update Produk Basi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        allowInput: true,
        disableMobile: "true"
    });

    const form = document.getElementById('spoiled-form');

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
        let totalBasi = 0;
        let totalKerugian = 0;

        document.querySelectorAll('.spoiled-input').forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            const productId = input.dataset.productId;
            const loss = qty * price;

            totalBasi += qty;
            totalKerugian += loss;

            // Update loss cell
            const lossCell = document.querySelector(`.loss-cell[data-product-id="${productId}"]`);
            if (lossCell) {
                lossCell.textContent = loss > 0 ? 'Rp ' + loss.toLocaleString('id-ID') : 'Rp 0';
            }
        });

        const totalBasiInput = document.getElementById('total-basi');
        if (totalBasiInput) totalBasiInput.value = totalBasi;

        const totalKerugianInput = document.getElementById('total-kerugian');
        if (totalKerugianInput) totalKerugianInput.value = 'Rp ' + totalKerugian.toLocaleString('id-ID');
    }

    form.querySelectorAll('.spoiled-input').forEach(input => {
        input.addEventListener('input', recalcAll);
    });
});
</script>
@endpush
