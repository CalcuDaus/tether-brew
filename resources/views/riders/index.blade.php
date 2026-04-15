@extends('layouts.app')
@section('title', 'Kelola Rider')

@section('actions')
    <a href="{{ route('riders.create') }}" class="btn btn-primary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Rider</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body card-body-no-padding">
        @if($riders->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Gerobak</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riders as $rider)
                            <tr>
                                <td>#{{ $rider->id }}</td>
                                <td class="text-primary-semi">
                                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg> {{ $rider->name }}
                                </td>
                                <td>{{ $rider->email }}</td>
                                <td>
                                    @if($rider->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $rider->whatsapp) }}" target="_blank" class="text-xs-green">
                                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-0.15em;">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                                            </svg>
                                            {{ $rider->whatsapp }}
                                        </a>
                                    @else
                                        <span class="text-sm-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rider->carts->count() > 0)
                                        @foreach($rider->carts as $cart)
                                            <span class="badge badge-{{ $cart->status }}">{{ $cart->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-sm-muted">Belum ditugaskan</span>
                                    @endif
                                </td>
                                <td class="text-sm-muted">{{ $rider->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="flex-gap-2">
                                        <a href="{{ route('riders.edit', $rider) }}" class="btn btn-secondary btn-sm"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit</a>
                                        <form method="POST" action="{{ route('riders.destroy', $rider) }}" onsubmit="return confirm('Yakin hapus rider ini? Rider akan di-unassign dari semua gerobak.')">
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
                {{ $riders->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div class="empty-state-title">Belum Ada Rider</div>
                <div class="empty-state-text">Mulai tambahkan rider untuk mengelola gerobak</div>
                <a href="{{ route('riders.create') }}" class="btn btn-primary"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg> Tambah Rider</a>
            </div>
        @endif
    </div>
</div>
@endsection
