@extends('layouts.app')

@section('title', 'Monitoring Chat')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Seluruh Percakapan Rider & Customer</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Rider</th>
                        <th>Gerobak</th>
                        <th>Pesan Terakhir</th>
                        <th>Waktu</th>
                        <th style="text-align: center;">Total Pesan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conversations as $conv)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $conv->customer->name ?? '-' }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $conv->customer->phone ?? '' }}</div>
                            </td>
                            <td>{{ $conv->rider->name ?? '-' }}</td>
                            <td>{{ $conv->cart->name ?? '-' }}</td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $conv->latestMessage->body ?? '-' }}
                            </td>
                            <td style="white-space: nowrap;">
                                {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '-' }}
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-block; padding: 2px 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">{{ $conv->messages_count }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.chats.show', $conv->id) }}" class="btn btn-primary btn-sm flex-center" style="gap: 0.4rem; white-space: nowrap;">
                                    <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">Belum ada percakapan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($conversations->hasPages())
            <div style="margin-top: 1rem; display: flex; justify-content: center;">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
