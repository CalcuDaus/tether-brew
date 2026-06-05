@extends('layouts.app')

@section('title', 'Riwayat Slip Gaji')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 15px; margin-bottom: 1.5rem;">
            <form method="GET" action="{{ route('admin.payroll.history') }}" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin: 0; flex: 1; min-width: 280px;" class="print-hide">
                <div class="form-group" style="width: 200px; margin-bottom: 0;">
                    <label class="form-label">Rider</label>
                    <select name="rider_id" class="form-input">
                        <option value="">Semua Rider</option>
                        @foreach($riders as $rider)
                            <option value="{{ $rider->id }}" {{ request('rider_id') == $rider->id ? 'selected' : '' }}>{{ $rider->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="width: 150px; margin-bottom: 0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; gap: 8px; margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
                    <a href="{{ route('admin.payroll.history') }}" class="btn btn-secondary" style="height: 42px; display: flex; align-items: center;">Reset</a>
                </div>
                <div class="form-group" style="width: 200px; margin-bottom: 0;">
                    <label class="form-label">Cari</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Ketik & tekan Enter..." style="padding-right: 3rem; border-radius: 12px; margin: 0; width: 100%;">
                        <button type="submit" class="search-btn-icon" title="Cari">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="table-container no-search" style="width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Rider</th>
                        <th>Tipe</th>
                        <th>Periode</th>
                        <th style="text-align: right;">Gaji Kotor</th>
                        <th style="text-align: right;">Potongan</th>
                        <th style="text-align: right;">THP</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td style="color: #64748b; font-size: 0.85rem;">#{{ str_pad($record->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td style="font-weight: 600;">{{ $record->rider->name }}</td>
                        <td>
                            <span class="badge" style="background: {{ $record->type === 'monthly' ? '#8b5c2a' : ($record->type === 'weekly' ? '#3b82f6' : '#6366f1') }}; color: white;">
                                {{ ucfirst($record->type) }}
                            </span>
                        </td>
                        <td>{{ $record->period_start->format('d/m') }} — {{ $record->period_end->format('d/m/Y') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($record->gross_income, 0, ',', '.') }}</td>
                        <td style="text-align: right; color: #ef4444;">Rp {{ number_format($record->kasbon_deducted + $record->minus_deducted, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 600;">Rp {{ number_format($record->net_income, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge" style="background: {{ $record->status === 'confirmed' ? '#22c55e' : '#eab308' }}; color: white;">
                                {{ $record->status === 'confirmed' ? 'Lunas' : 'Draft' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.payroll.show', $record->id) }}" class="btn btn-outline-primary">
                                <svg class="icon-two-tone" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 2px;">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 30px;">Belum ada riwayat slip gaji.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($records->hasPages())
        <div class="pagination-container print-hide" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px 20px; border-top: 1px solid #e2e8f0;">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
                Menampilkan <span>{{ $records->firstItem() }} - {{ $records->lastItem() }}</span> dari {{ $records->total() }} data
            </div>
            <div style="display: flex; gap: 5px;">
                @if($records->onFirstPage())
                    <button class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; opacity: 0.5;" disabled>Sebelumnya</button>
                @else
                    <a href="{{ $records->previousPageUrl() }}" class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; display: inline-flex; align-items: center;">Sebelumnya</a>
                @endif
                <div style="display: flex; gap: 5px;">
                    @php
                        $startPage = max(1, $records->currentPage() - 2);
                        $endPage = min($records->lastPage(), $startPage + 4);
                        if ($endPage - $startPage < 4) { $startPage = max(1, $endPage - 4); }
                    @endphp
                    @for($i = $startPage; $i <= $endPage; $i++)
                        @if($i == $records->currentPage())
                            <button class="btn btn-sm" style="background: #8b5c2a; color: white; border: 1px solid #8b5c2a;" disabled>{{ $i }}</button>
                        @else
                            <a href="{{ $records->url($i) }}" class="btn btn-sm" style="background: white; color: #475569; border: 1px solid #cbd5e1; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; min-width: 32px;">{{ $i }}</a>
                        @endif
                    @endfor
                </div>
                @if($records->hasMorePages())
                    <a href="{{ $records->nextPageUrl() }}" class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; display: inline-flex; align-items: center;">Selanjutnya</a>
                @else
                    <button class="btn btn-sm" style="background: white; border: 1px solid #cbd5e1; color: #475569; opacity: 0.5;" disabled>Selanjutnya</button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
