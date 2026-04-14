@extends('layouts.app')
@section('title', 'Kelola Stok')

@section('content')
<div class="grid-2">
    {{-- Cart selector --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg> Pilih Gerobak</h3>
        </div>
        <div class="card-body">
            @foreach($carts as $cart)
                <a href="{{ route('inventories.index', ['cart_id' => $cart->id]) }}"
                   class="nav-link {{ $selectedCart && $selectedCart->id == $cart->id ? 'active' : '' }} mb-1-custom">
                    <span class="nav-link-icon"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg></span>
                    <div>
                        <div class="text-primary-semi">{{ $cart->name }}</div>
                        <div class="text-xs-muted">{{ $cart->user?->name ?? 'Tanpa rider' }} · {{ $cart->inventories->count() }} produk</div>
                    </div>
                </a>
            @endforeach

            @if($carts->isEmpty())
                <div class="empty-state p-5-custom">
                    <div class="empty-state-text">Belum ada gerobak</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Stock editor --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                </svg> {{ $selectedCart ? 'Stok: ' . $selectedCart->name : 'Pilih gerobak untuk edit stok' }}
            </h3>
        </div>
        <div class="card-body">
            @if($selectedCart)
                <form method="POST" action="{{ route('inventories.update') }}">
                    @csrf
                    <input type="hidden" name="cart_id" value="{{ $selectedCart->id }}">

                    @foreach($products as $i => $product)
                        @php
                            $currentStock = $selectedCart->inventories->firstWhere('product_id', $product->id)?->stock ?? 0;
                        @endphp
                        <div class="flex-between py-3-custom border-bottom">
                            <div>
                                <div class="text-primary-semi">{{ $product->name }}</div>
                                <div class="text-sm-muted">{{ $product->category }} · Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex-start-gap">
                                <input type="hidden" name="stocks[{{ $i }}][product_id]" value="{{ $product->id }}">
                                <input type="number" name="stocks[{{ $i }}][stock]" class="form-input text-center p-2"
                                       value="{{ $currentStock }}" min="0"
                                      >
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary mt-5-custom w-full-custom flex-center">
                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg> Simpan Stok
                    </button>
                </form>
            @else
                <div class="empty-state p-5-custom">
                    <div class="empty-state-icon">
                        <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                        </svg>
                    </div>
                    <div class="empty-state-text">Pilih gerobak di daftar sebelah kiri</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

