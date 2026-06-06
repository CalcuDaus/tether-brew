@extends('layouts.app')

@section('title', 'Sisa Stok Tersedia')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            Sisa Stok (Total Produksi)
        </h3>
    </div>

    {{-- Date Filter --}}
    <div class="card-body" style="border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
        <form method="GET" action="{{ route('admin.available_stocks.index') }}"
            style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Tampilkan Stok Hingga Tanggal</label>
                <div class="flatpickr-input-container">
                    <input type="text" name="date" id="filter_date" class="form-input datepicker"
                        value="{{ request('date') ?: date('Y-m-d') }}" placeholder="Pilih tanggal...">
                </div>
            </div>
            <div class="form-group" style="display: flex; gap: 8px; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Terapkan</button>
            </div>
        </form>
    </div>

    {{-- Total Produksi (Available Stock) Cards --}}
    <div class="card-body">
        <div id="stock-cards-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
            <!-- Cards will be injected here via JS -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('filter_date');

        flatpickr("#filter_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F Y",
            allowInput: true,
            disableMobile: "true",
            onChange: function(selectedDates, dateStr, instance) {
                fetchAvailableStock(dateStr);
            }
        });

        const stockContainer = document.getElementById('stock-cards-container');
        
        async function fetchAvailableStock(dateStr = '') {
            if (!stockContainer) return;
            
            const date = dateStr || dateInput.value;
            
            try {
                stockContainer.innerHTML = '<div style="color: var(--text-muted); font-size: 0.9rem; padding: 5px; grid-column: 1 / -1;">Memuat data stok...</div>';
                
                const response = await fetch(`{{ route('admin.rider_sales.available_stock') }}?date=${date}`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                const stockData = Array.isArray(data) ? data : (data.stockData || []);
                
                stockContainer.innerHTML = '';
                
                if (stockData.length === 0) {
                    stockContainer.innerHTML = '<div style="color: var(--text-muted); font-size: 0.9rem; padding: 5px; grid-column: 1 / -1;">Tidak ada data stok produksi.</div>';
                    return;
                }
                
                stockData.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'stock-card';
                    
                    let bg = 'rgba(34, 197, 94, 0.04)';
                    let border = 'rgba(34, 197, 94, 0.15)';
                    let text = '#22c55e';
                    
                    if (item.available === 0) {
                        bg = 'rgba(239, 68, 68, 0.04)';
                        border = 'rgba(239, 68, 68, 0.15)';
                        text = '#ef4444';
                    } else if (item.available < 5) {
                        bg = 'rgba(245, 158, 11, 0.04)';
                        border = 'rgba(245, 158, 11, 0.15)';
                        text = '#f59e0b';
                    }
                    
                    card.style.cssText = `
                        background: ${bg};
                        border: 1px solid ${border};
                        border-radius: 10px;
                        padding: 16px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
                        transition: all 0.2s ease;
                        display: flex;
                        flex-direction: column;
                    `;
                    
                    card.innerHTML = `
                        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">${item.product_name}</div>
                        <div style="font-size: 2rem; font-weight: 800; color: ${text}; margin: 8px 0; display: flex; align-items: baseline; gap: 4px;">
                            ${item.available}
                            <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Pcs</span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); border-top: 1px dashed var(--border-color); padding-top: 8px; margin-top: auto; display: flex; flex-direction: column; gap: 4px;">
                            <div style="display: flex; justify-content: space-between;"><span>Total Prod:</span> <strong>${item.produced}</strong></div>
                            <div style="display: flex; justify-content: space-between;"><span>Total Out:</span> <strong>${item.used}</strong></div>
                            <div style="display: flex; justify-content: space-between;"><span>Total Basi:</span> <strong>${item.spoiled}</strong></div>
                        </div>
                    `;
                    stockContainer.appendChild(card);
                });
            } catch (error) {
                console.error('Error fetching stock:', error);
                stockContainer.innerHTML = '<div style="color: #ef4444; font-size: 0.9rem; padding: 5px; grid-column: 1 / -1;">Gagal memuat data stok.</div>';
            }
        }

        // Initial fetch
        fetchAvailableStock();
    });
</script>
@endpush
