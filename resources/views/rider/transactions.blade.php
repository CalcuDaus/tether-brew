@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('actions')
    <a href="{{ route('rider.pos') }}" class="btn btn-primary btn-sm">
        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
        </svg> Buat Transaksi
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($transactions->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Catatan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            <tr>
                                <td>#{{ $tx->id }}</td>
                                <td>
                                    @foreach($tx->items as $item)
                                        <div class="text-md-muted">
                                            {{ $item->product->name ?? '-' }}
                                            <span class="text-muted-custom">× {{ $item->qty }}</span>
                                            <span class="text-gold-semi">= Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-gold-bold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td><span class="badge badge-{{ $tx->payment_method }}">{{ strtoupper($tx->payment_method) }}</span></td>
                                <td class="text-sm-muted">{{ $tx->notes ?? '-' }}</td>
                                <td class="text-md-muted">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4-custom">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Riwayat Transaksi</div>
                <div class="empty-state-text">Buat transaksi pertama melalui POS</div>
                <a href="{{ route('rider.pos') }}" class="btn btn-primary">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
                    </svg> Buat Transaksi
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

