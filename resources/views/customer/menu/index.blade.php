@extends('layouts.customer')

@section('title', 'Menu - Tether Brew')

@push('styles')
<style>
    .menu-header {
        padding: 20px;
        background: white;
        position: sticky;
        top: 0;
        z-index: 40;
        border-bottom: 1px solid #f1f5f9;
    }
    .menu-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .products-container {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .product-card {
        display: flex;
        background: white;
        border-radius: 16px;
        padding: 15px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        gap: 15px;
    }

    .product-img {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        background: #f8fafc;
    }

    .product-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .product-name {
        font-weight: 700;
        font-size: 1.05rem;
        color: #1e293b;
        margin: 0 0 4px 0;
    }

    .product-desc {
        font-size: 0.8rem;
        color: #64748b;
        margin: 0 0 8px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-weight: 700;
        color: #8b5c2a;
        font-size: 0.95rem;
    }

    .order-btn {
        background: #8b5c2a;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
    <div class="menu-header">
        <h1 class="menu-title">Menu Pilihan</h1>
    </div>

    <div class="products-container">
        @forelse($products as $product)
            <div class="product-card">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-img">
                @else
                    <div class="product-img" style="display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    </div>
                @endif
                
                <div class="product-info">
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <p class="product-desc">{{ $product->description ?? 'Kopi nikmat spesial dari Tether Brew.' }}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <button class="order-btn" onclick="Swal.fire({toast:true, position:'top-end', icon:'info', title:'Silakan temui gerobak terdekat untuk memesan!', showConfirmButton:false, timer:3000})">Pesan</button>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                Belum ada produk yang tersedia.
            </div>
        @endforelse
    </div>
@endsection
