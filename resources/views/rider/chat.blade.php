@extends('layouts.app')

@section('title', 'Chat')

@push('styles')
<style>
    .chat-list-container {
        max-width: 720px;
    }
    .chat-conversation-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .chat-conv-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.2rem;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .chat-conv-item:hover {
        border-color: var(--accent);
        transform: translateX(4px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .chat-conv-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), #f97316);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .chat-conv-info {
        flex: 1;
        min-width: 0;
    }
    .chat-conv-name {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.15rem;
    }
    .chat-conv-preview {
        font-size: 0.8rem;
        opacity: 0.6;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-conv-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.3rem;
        flex-shrink: 0;
    }
    .chat-conv-time {
        font-size: 0.72rem;
        opacity: 0.5;
    }
    .chat-conv-badge {
        background: var(--accent);
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 10px;
        min-width: 20px;
        text-align: center;
    }
    .chat-empty-state {
        text-align: center;
        padding: 4rem 2rem;
        opacity: 0.5;
    }
    .chat-empty-state svg {
        margin-bottom: 1rem;
        opacity: 0.4;
    }
    .chat-empty-state p {
        font-size: 0.9rem;
    }
    .chat-conv-cart {
        font-size: 0.72rem;
        opacity: 0.5;
        margin-top: 0.1rem;
    }
</style>
@endpush

@section('content')
<div class="chat-list-container" id="chat-list-container">
    @if($conversations->isEmpty())
        <div class="chat-empty-state" id="chat-empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <p>Belum ada percakapan.<br>Customer akan muncul di sini saat mereka mengirim pesan.</p>
        </div>
    @else
        <div class="chat-conversation-list" id="chat-conversation-list">
            @foreach($conversations as $conv)
                <a href="{{ route('rider.chat.show', $conv) }}" class="chat-conv-item">
                    <div class="chat-conv-avatar">{{ strtoupper(substr($conv->customer->name ?? '?', 0, 1)) }}</div>
                    <div class="chat-conv-info">
                        <div class="chat-conv-name">{{ $conv->customer->name ?? 'Customer' }}</div>
                        <div class="chat-conv-preview">
                            {{ $conv->latestMessage?->body ?? 'Belum ada pesan' }}
                        </div>
                        @if($conv->cart)
                            <div class="chat-conv-cart">📍 {{ $conv->cart->name }}</div>
                        @endif
                    </div>
                    <div class="chat-conv-meta">
                        @if($conv->last_message_at)
                            <span class="chat-conv-time">{{ $conv->last_message_at->diffForHumans() }}</span>
                        @endif
                        @if($conv->unread_count > 0)
                            <span class="chat-conv-badge">{{ $conv->unread_count }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    let chatListPollingTimer = null;

    function escapeHtmlChatList(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '';
        const now = new Date();
        const date = new Date(dateStr);
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);

        if (diffSec < 60) return 'baru saja';
        if (diffMin < 60) return diffMin + ' menit yang lalu';
        if (diffHour < 24) return diffHour + ' jam yang lalu';
        if (diffDay < 7) return diffDay + ' hari yang lalu';
        return date.toLocaleDateString('id-ID');
    }

    function renderConversationList(conversations) {
        const container = document.getElementById('chat-list-container');
        if (!container) return;

        if (conversations.length === 0) {
            container.innerHTML = `
                <div class="chat-empty-state" id="chat-empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <p>Belum ada percakapan.<br>Customer akan muncul di sini saat mereka mengirim pesan.</p>
                </div>`;
            return;
        }

        let html = '<div class="chat-conversation-list" id="chat-conversation-list">';
        conversations.forEach(conv => {
            const customerName = conv.customer?.name ?? 'Customer';
            const initial = (customerName[0] || '?').toUpperCase();
            const preview = conv.latest_message?.body ? escapeHtmlChatList(conv.latest_message.body) : 'Belum ada pesan';
            const cartName = conv.cart?.name ?? '';
            const time = timeAgo(conv.last_message_at);
            const unread = conv.unread_count || 0;

            html += `<a href="/rider/chat/${conv.id}" class="chat-conv-item">
                <div class="chat-conv-avatar">${initial}</div>
                <div class="chat-conv-info">
                    <div class="chat-conv-name">${escapeHtmlChatList(customerName)}</div>
                    <div class="chat-conv-preview">${preview}</div>
                    ${cartName ? `<div class="chat-conv-cart">📍 ${escapeHtmlChatList(cartName)}</div>` : ''}
                </div>
                <div class="chat-conv-meta">
                    ${time ? `<span class="chat-conv-time">${time}</span>` : ''}
                    ${unread > 0 ? `<span class="chat-conv-badge">${unread}</span>` : ''}
                </div>
            </a>`;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    async function pollChatList() {
        try {
            const res = await fetch('/rider/chat/conversations-json?_t=' + Date.now(), {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
            });
            if (!res.ok) return;
            const data = await res.json();
            renderConversationList(data.conversations || []);
        } catch (err) {
            // silently ignore polling errors
        }
    }

    function startChatListPolling() {
        if (chatListPollingTimer) clearInterval(chatListPollingTimer);
        chatListPollingTimer = setInterval(pollChatList, 3000);
    }

    // Start polling on page load
    startChatListPolling();

    // Stop polling when leaving
    window.addEventListener('beforeunload', () => {
        if (chatListPollingTimer) clearInterval(chatListPollingTimer);
    });
</script>
@endpush
