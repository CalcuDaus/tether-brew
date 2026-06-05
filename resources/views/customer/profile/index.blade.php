@extends('layouts.customer')

@section('title', 'Me - Tether Brew')

@push('styles')
<style>
    .me-header {
        padding: 30px 20px;
        background: linear-gradient(135deg, #8b5c2a, #6b4420);
        color: white;
        text-align: center;
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: white;
        color: #8b5c2a;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px auto;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .profile-name {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 5px 0;
    }
    .profile-phone {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0;
    }

    .section-block {
        padding: 20px;
        background: white;
        margin-top: 10px;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 15px 0;
    }

    .history-card {
        padding: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 10px;
    }
    .history-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .history-title {
        font-weight: 600;
        color: #1e293b;
    }
    .history-date {
        font-size: 0.8rem;
        color: #64748b;
    }
    .history-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .history-price {
        font-weight: 700;
        color: #8b5c2a;
    }
    .history-status {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
        background: #dcfce7;
        color: #16a34a;
        font-weight: 600;
    }

    .logout-btn {
        display: block;
        width: 100%;
        padding: 15px;
        background: white;
        color: #ef4444;
        border: none;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
        font-weight: 600;
        font-size: 1rem;
        margin-top: 20px;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
    <div class="me-header">
        <div class="profile-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <h1 class="profile-name">{{ auth()->user()->name }}</h1>
        <p class="profile-phone">{{ auth()->user()->phone }}</p>
    </div>

    <div class="section-block">
        <h2 class="section-title">Riwayat Pemesanan</h2>
        @forelse($transactions as $transaction)
            <div class="history-card">
                <div class="history-header">
                    <span class="history-title">{{ $transaction->cart->name ?? 'Gerobak' }}</span>
                    <span class="history-date">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="history-body">
                    <span class="history-price">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                    <span class="history-status">Selesai</span>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: #64748b; padding: 20px;">
                Belum ada riwayat pemesanan.
            </div>
        @endforelse
    </div>

    <form method="POST" action="{{ route('customer.logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            Keluar (Logout)
        </button>
    </form>
@endsection
