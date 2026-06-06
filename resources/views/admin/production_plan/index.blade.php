@extends('layouts.app')

@section('title', 'Plan Produksi')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            Plan Produksi
        </h3>
    </div>

    {{-- Date Filter --}}
    <div class="card-body" style="border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
        <form method="GET" action="{{ route('admin.production_plan.index') }}"
            style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Tampilkan Plan Berdasarkan Penjualan Tanggal</label>
                <div class="flatpickr-input-container">
                    <input type="text" name="date" id="filter_date" class="form-input datepicker"
                        value="{{ request('date') ?: today()->subDay()->format('Y-m-d') }}" placeholder="Pilih tanggal...">
                </div>
            </div>
            <div class="form-group" style="display: flex; gap: 8px; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Terapkan</button>
            </div>
        </form>
    </div>

    {{-- Plan Produksi Cards --}}
    <div class="card-body">
        <div id="plan-cards-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
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
                fetchProductionPlan(dateStr);
            }
        });

        const planContainer = document.getElementById('plan-cards-container');
        
        async function fetchProductionPlan(dateStr = '') {
            if (!planContainer) return;
            
            const date = dateStr || dateInput.value;
            
            try {
                planContainer.innerHTML = '<div style="color: var(--text-muted); font-size: 0.9rem; padding: 5px; grid-column: 1 / -1;">Memuat data plan produksi...</div>';
                
                const response = await fetch(`{{ route('admin.production_plan.data') }}?date=${date}`);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                const planData = Array.isArray(data) ? data : (data.planData || []);
                
                planContainer.innerHTML = '';
                
                if (planData.length === 0) {
                    planContainer.innerHTML = '<div style="color: var(--text-muted); font-size: 0.9rem; padding: 5px; grid-column: 1 / -1;">Tidak ada data penjualan untuk tanggal tersebut.</div>';
                    return;
                }
                
                planData.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'stock-card';
                    
                    let bg = 'rgba(59, 130, 246, 0.04)';
                    let border = 'rgba(59, 130, 246, 0.15)';
                    let text = '#3b82f6'; // blue
                    
                    if (item.plan_qty === 0) {
                        bg = 'rgba(107, 114, 128, 0.04)';
                        border = 'rgba(107, 114, 128, 0.15)';
                        text = '#6b7280'; // gray
                    } else if (item.plan_qty > 20) {
                        bg = 'rgba(34, 197, 94, 0.04)';
                        border = 'rgba(34, 197, 94, 0.15)';
                        text = '#22c55e'; // green
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
                            ${item.plan_qty}
                            <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Pcs</span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); border-top: 1px dashed var(--border-color); padding-top: 8px; margin-top: auto; display: flex; flex-direction: column; gap: 4px;">
                            <div style="display: flex; justify-content: space-between;"><span>Terjual Kemarin:</span> <strong>${item.plan_qty}</strong></div>
                        </div>
                    `;
                    planContainer.appendChild(card);
                });
            } catch (error) {
                console.error('Error fetching plan:', error);
                planContainer.innerHTML = '<div style="color: #ef4444; font-size: 0.9rem; padding: 5px; grid-column: 1 / -1;">Gagal memuat data plan produksi.</div>';
            }
        }

        // Initial fetch
        fetchProductionPlan();
    });
</script>
@endpush
