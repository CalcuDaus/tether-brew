@extends('layouts.app')
@section('title', 'Semua Transaksi')

@section('content')
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($transactions->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gerobak</th>
                            <th>Rider</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                            <tr>
                                <td>#{{ $tx->id }}</td>
                                <td class="text-primary-semi">{{ $tx->cart->name ?? '-' }}</td>
                                <td>{{ $tx->user->name ?? '-' }}</td>
                                <td>
                                    @foreach($tx->items as $item)
                                        <div class="text-sm-muted">
                                            {{ $item->product->name ?? '-' }} × {{ $item->qty }}
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-gold-bold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td><span class="badge badge-{{ $tx->payment_method }}">{{ strtoupper($tx->payment_method) }}</span></td>
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
                        <circle cx="12" cy="12" r="8"/><line x1="12" x2="12" y1="16" y2="8"/><line x1="10" x2="14" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="14"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Transaksi</div>
                <div class="empty-state-text">Transaksi akan muncul setelah rider membuat penjualan via POS</div>
            </div>
        @endif
    </div>
</div>
@endsection

