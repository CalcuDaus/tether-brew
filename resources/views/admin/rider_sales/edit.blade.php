@extends('layouts.app')

@section('title', 'Edit Penjualan Rider')

@section('content')
<div class="card mb-4" style="max-width: 1400px; margin: 0 auto; background: var(--bg-card);">
    <div class="card-header" style="text-align: center; border-bottom: 2px solid var(--border-color); padding: 20px;">
        <h2 style="font-weight: 700; color: var(--text-primary); margin: 0; font-size: 1.5rem; letter-spacing: 1px;">TETHER BREW OFFICE</h2>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.rider_sales.update', $riderSale->id) }}" method="POST" id="sales-form">
            @csrf
            @method('PUT')

            <!-- Top Row: Date & Rider -->
            <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px;">
                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label class="form-label" style="font-weight: 600; text-transform: uppercase;">TANGGAL</label>
                    <div class="flatpickr-input-container">
                        <input type="text" name="date" id="date" class="form-input" value="{{ $riderSale->date->format('Y-m-d') }}" required placeholder="Pilih tanggal..." style="height: 48px; font-size: 1rem;">
                        <svg width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                </div>
                <div class="form-group" style="flex: 1; min-width: 250px;">
                    <label class="form-label" style="font-weight: 600; text-transform: uppercase;">NAMA RIDER</label>
                    <select name="rider_id" class="form-input" required style="height: 48px; font-size: 1rem;">
                        <option value="">-- Pilih Rider --</option>
                        @foreach($riders as $rider)
                            <option value="{{ $rider->id }}" {{ $riderSale->rider_id == $rider->id ? 'selected' : '' }}>{{ $rider->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Matrix Table -->
            <div class="table-container" style="margin-bottom: 40px; border: 1px solid var(--border-color); border-radius: 12px; overflow-x: auto;">
                <table class="table" style="min-width: 1400px; margin: 0; white-space: nowrap;">
                    <thead style="background: rgba(0,0,0,0.02);">
                        <tr>
                            <th style="padding: 15px; font-weight: 600; border-right: 1px solid var(--border-color); position: sticky; left: 0; background: var(--bg-card); z-index: 10;">Nama Produk</th>
                            @foreach($products as $product)
                                <th style="padding: 15px; font-weight: 600; text-align: center; border-right: 1px solid var(--border-color); min-width: 100px;">
                                    {{ $product->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rows = [
                                'stock_out' => 'Produk Keluar',
                                'stock_added' => 'Produk Tambahan',
                                'stock_return' => 'Produk Retur',
                                'stock_sold' => 'Produk Laku',
                            ];
                        @endphp
                        @foreach($rows as $key => $label)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 12px 15px; font-weight: 600; border-right: 1px solid var(--border-color); position: sticky; left: 0; background: var(--bg-card); z-index: 10;">
                                    {{ $label }}
                                </td>
                                @foreach($products as $product)
                                    <td style="padding: 8px; text-align: center; border-right: 1px solid var(--border-color);">
                                        @php
                                            $itemData = $riderSale->items->where('product_id', $product->id)->first();
                                            $val = $itemData ? $itemData->$key : 0;
                                        @endphp
                                        <input type="number" 
                                               name="items[{{ $product->id }}][{{ $key }}]" 
                                               class="form-input item-input {{ $key }}"
                                               data-product-id="{{ $product->id }}"
                                               data-price="{{ $product->price }}"
                                               min="0" 
                                               value="{{ $val }}"
                                               {{ $key === 'stock_sold' ? 'readonly' : '' }}
                                               style="width: 80px; text-align: center; margin: 0 auto; padding: 6px; font-size: 0.95rem; {{ $key === 'stock_sold' ? 'background: rgba(34,197,94,0.08); font-weight: 700; cursor: not-allowed;' : '' }}">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 2px solid var(--border-color); background: rgba(0,0,0,0.03);">
                            <td style="padding: 12px 15px; font-weight: 700; border-right: 1px solid var(--border-color); position: sticky; left: 0; background: var(--bg-card); z-index: 10;">
                                Total Uang
                            </td>
                            @foreach($products as $product)
                                <td style="padding: 12px 8px; text-align: center; border-right: 1px solid var(--border-color); font-weight: 700; color: #22c55e;">
                                    <span class="total-uang-cell" data-product-id="{{ $product->id }}" data-price="{{ $product->price }}">
                                        @php
                                            $itemData = $riderSale->items->where('product_id', $product->id)->first();
                                            $sold = $itemData ? $itemData->stock_sold : 0;
                                            $total = $sold * $product->price;
                                        @endphp
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Summary Section -->
            <div style="margin-bottom: 30px; padding: 25px; background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: 12px;">
                <h4 style="margin: 0 0 20px; font-weight: 700; font-size: 1.2rem; letter-spacing: 1px;">TOTAL</h4>
                
                <!-- Row 1: CASH & QRIS side by side -->
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <div class="form-group" style="flex: 1; margin: 0;">
                        <label class="form-label" style="font-weight: 600;">CASH</label>
                        <input type="number" name="cash_amount" id="cash-amount" class="form-input" value="{{ $riderSale->cash_amount }}" style="height: 44px; font-size: 1rem; font-weight: 600;">
                    </div>
                    <div class="form-group" style="flex: 1; margin: 0;">
                        <label class="form-label" style="font-weight: 600;">QRIS</label>
                        <input type="number" name="qris_amount" class="form-input" value="{{ $riderSale->qris_amount }}" style="height: 44px; font-size: 1rem; font-weight: 600;">
                    </div>
                </div>

                <!-- Row 1.5: Actual Setor & Minus -->
                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <div class="form-group" style="flex: 1; margin: 0;">
                        <label class="form-label" style="font-weight: 600;">Actual Setor</label>
                        <input type="number" name="actual_setor" id="actual-setor" class="form-input" value="{{ $riderSale->actual_setor }}" required style="height: 44px; font-size: 1rem; font-weight: 600; border-color: #3b82f6;">
                    </div>
                    <div class="form-group" style="flex: 1; margin: 0;">
                        <label class="form-label" style="font-weight: 600; color: #ef4444;">Minus</label>
                        <input type="number" name="minus_amount" id="minus-amount" class="form-input" readonly value="{{ $riderSale->minus_amount }}"
                               style="height: 44px; font-size: 1rem; font-weight: 600; background: rgba(239,68,68,0.08); color: #ef4444; cursor: not-allowed;">
                    </div>
                </div>
                
                <!-- Row 2: TOTAL (auto-calculated, read-only) -->
                <div class="form-group" style="margin: 0;">
                    <label class="form-label" style="font-weight: 600;">TOTAL</label>
                    <input type="number" name="total_gross_income" id="total-gross-income" class="form-input" readonly value="{{ $riderSale->total_gross_income }}"
                           style="height: 44px; font-size: 1.1rem; font-weight: 700; background: rgba(34,197,94,0.08); cursor: not-allowed;">
                </div>
            </div>

            <!-- Admin Pemeriksa & Action Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
                <div class="form-group" style="flex: 1; max-width: 300px; margin: 0;">
                    <label class="form-label" style="font-weight: 600;">Nama Admin Pemeriksa</label>
                    <input type="text" name="admin_pemeriksa" class="form-input" value="{{ $riderSale->admin_pemeriksa ?? auth()->user()->name }}" style="height: 48px; font-size: 1rem;">
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button type="reset" class="btn btn-secondary" style="height: 48px; padding: 0 30px; font-size: 1rem; font-weight: 600;">Reset</button>
                    <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 40px; font-size: 1rem; font-weight: 600;">Update</button>
                </div>
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

    const form = document.getElementById('sales-form');

    // Prevent scroll-wheel changing number input values
    form.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('wheel', (e) => e.preventDefault());
    });

    // Get all unique product IDs from the table
    const productIds = [...new Set(
        [...form.querySelectorAll('.item-input')].map(el => el.dataset.productId)
    )];

    /**
     * For a given product ID, get the value of an input by its row class.
     * rowClass is one of: 'stock_out', 'stock_added', 'stock_return', 'stock_sold'
     */
    function getVal(productId, rowClass) {
        const input = form.querySelector(`.item-input.${rowClass}[data-product-id="${productId}"]`);
        return input ? (parseInt(input.value) || 0) : 0;
    }

    /**
     * Recalculate everything for one product column.
     */
    function recalcProduct(productId) {
        const stockOut = getVal(productId, 'stock_out');
        const stockAdded = getVal(productId, 'stock_added');
        const stockReturn = getVal(productId, 'stock_return');

        // 1. Auto-fill Produk Laku = (Keluar + Tambahan) - Retur
        const soldInput = form.querySelector(`.item-input.stock_sold[data-product-id="${productId}"]`);
        const stockSold = Math.max(0, stockOut + stockAdded - stockReturn);
        if (soldInput) {
            soldInput.value = stockSold;
        }

        // 2. Update Total Uang cell = harga × produk_laku
        const totalUangCell = document.querySelector(`.total-uang-cell[data-product-id="${productId}"]`);
        if (totalUangCell) {
            const price = parseFloat(totalUangCell.dataset.price) || 0;
            const totalUang = price * stockSold;
            totalUangCell.textContent = 'Rp ' + totalUang.toLocaleString('id-ID');
        }
    }

    /**
     * Recalculate the grand total (sum of all Total Uang cells).
     * This value goes into the TOTAL input (#total-gross-income).
     */
    function recalcGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.total-uang-cell').forEach(cell => {
            const price = parseFloat(cell.dataset.price) || 0;
            const productId = cell.dataset.productId;
            const stockSold = getVal(productId, 'stock_sold');
            grandTotal += price * stockSold;
        });
        const totalInput = document.getElementById('total-gross-income');
        if (totalInput) {
            totalInput.value = grandTotal;
        }
    }

    /**
     * Main event listener: when any input in rows stock_out, stock_added, 
     * or stock_return changes, recalc that product column + grand total.
     */
    form.querySelectorAll('.item-input.stock_out, .item-input.stock_added, .item-input.stock_return').forEach(input => {
        input.addEventListener('input', () => {
            recalcProduct(input.dataset.productId);
            recalcGrandTotal();
        });
    });

    // Minus Calculation
    const cashInput = document.getElementById('cash-amount');
    const actualSetorInput = document.getElementById('actual-setor');
    const minusInput = document.getElementById('minus-amount');

    function calcMinus() {
        const cash = parseFloat(cashInput.value) || 0;
        const actual = parseFloat(actualSetorInput.value) || 0;
        const minus = Math.max(0, cash - actual);
        if (minusInput) {
            minusInput.value = minus;
        }
    }

    if (cashInput && actualSetorInput) {
        cashInput.addEventListener('input', calcMinus);
        actualSetorInput.addEventListener('input', calcMinus);
    }
});
</script>
@endpush
