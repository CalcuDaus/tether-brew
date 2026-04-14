@extends('layouts.app')
@section('title', 'Kelola Produk')

@section('actions')
    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Produk</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($products->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>#{{ $product->id }}</td>
                                <td>
                                    <div class="text-primary-semi">
                                        <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                                        </svg> {{ $product->name }}
                                    </div>
                                    @if($product->description)
                                        <div class="text-sm-muted mt-2-custom">{{ Str::limit($product->description, 50) }}</div>
                                    @endif
                                </td>
                                <td><span class="badge badge-category">{{ $product->category }}</span></td>
                                <td class="text-gold-semi">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge badge-active">Aktif</span>
                                    @else
                                        <span class="badge badge-inactive">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex-gap-2">
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Yakin hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4-custom">
                {{ $products->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Produk</div>
                <div class="empty-state-text">Mulai tambahkan produk kopi Anda</div>
                <a href="{{ route('products.create') }}" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Produk</a>
            </div>
        @endif
    </div>
</div>
@endsection

