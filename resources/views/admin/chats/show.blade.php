@extends('layouts.app')

@section('title', 'Chat: ' . ($conversation->customer->name ?? 'Customer') . ' & ' . ($conversation->rider->name ?? 'Rider'))

@push('styles')
<style>
    .admin-chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        height: calc(100dvh - 120px);
        max-width: 720px;
        margin: 0 auto;
    }
    .admin-chat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.8rem 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        margin-bottom: 0.8rem;
    }
    .admin-chat-back {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        text-decoration: none;
        transition: all 0.2s;
    }
    .admin-chat-back:hover {
        border-color: var(--accent);
        color: var(--accent);
    }
    .admin-chat-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .admin-chat-avatar.customer {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .admin-chat-avatar.rider {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }
    .admin-chat-info h3 {
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
    }
    .admin-chat-info p {
        font-size: 0.75rem;
        opacity: 0.5;
        margin: 0;
    }
    .admin-chat-messages-area {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        padding: 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
    }
    .admin-chat-messages-area::-webkit-scrollbar {
        width: 4px;
    }
    .admin-chat-messages-area::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 4px;
    }
    .admin-chat-bubble {
        max-width: 75%;
        padding: 0.6rem 0.9rem;
        border-radius: 16px;
        font-size: 0.85rem;
        line-height: 1.5;
        animation: adminBubbleIn 0.2s ease-out;
    }
    .admin-chat-bubble.rider-msg {
        background: linear-gradient(135deg, #914a30, #6d3824);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }
    .admin-chat-bubble.customer-msg {
        background: rgba(128, 128, 128, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(128, 128, 128, 0.2);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }
    .admin-chat-bubble-sender {
        font-size: 0.65rem;
        font-weight: 700;
        margin-bottom: 0.15rem;
        opacity: 0.7;
    }
    .admin-chat-bubble-meta {
        font-size: 0.65rem;
        opacity: 0.6;
        margin-top: 0.2rem;
    }
    .admin-chat-readonly-bar {
        padding: 0.8rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        margin-top: 0.8rem;
        text-align: center;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    .admin-chat-loading {
        text-align: center;
        padding: 2rem;
        opacity: 0.5;
        font-size: 0.85rem;
    }
    @keyframes adminBubbleIn {
        from { opacity: 0; transform: translateY(8px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>
@endpush

@section('content')
<div class="admin-chat-container">
    <div class="admin-chat-header">
        <a href="{{ route('admin.chats.index') }}" class="admin-chat-back">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </a>
        <div class="admin-chat-avatar customer">{{ strtoupper(substr($conversation->customer->name ?? '?', 0, 1)) }}</div>
        <div class="admin-chat-info" style="flex: 1;">
            <h3>{{ $conversation->customer->name ?? 'Customer' }}</h3>
            <p>{{ $conversation->customer->phone ?? '' }}</p>
        </div>
        <div style="font-size: 0.8rem; opacity: 0.5;">↔</div>
        <div class="admin-chat-avatar rider">{{ strtoupper(substr($conversation->rider->name ?? '?', 0, 1)) }}</div>
        <div class="admin-chat-info">
            <h3>{{ $conversation->rider->name ?? 'Rider' }}</h3>
            <p>Rider{{ $conversation->cart ? ' · ' . $conversation->cart->name : '' }}</p>
        </div>
    </div>

    <div class="admin-chat-messages-area" id="admin-chat-messages">
        <div class="admin-chat-loading" id="admin-chat-loading">Memuat pesan...</div>
    </div>

    <div class="admin-chat-readonly-bar">
        <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -0.15em; margin-right: 0.3rem;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Mode baca saja — Admin tidak dapat mengirim pesan.
    </div>
</div>
@endsection

@push('scripts')
<script>
    const conversationId = {{ $conversation->id }};
    const riderId = {{ $conversation->rider_id }};
    const messagesContainer = document.getElementById('admin-chat-messages');

    let allMessages = [];

    async function loadMessages() {
        try {
            const res = await fetch(`/admin/chats/${conversationId}/messages?_t=${Date.now()}`, {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
            });
            const data = await res.json();
            allMessages = (data.data || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            renderMessages();
            scrollToBottom();
        } catch (err) {
            document.getElementById('admin-chat-loading').textContent = 'Gagal memuat pesan.';
        }
    }

    function renderMessages() {
        if (allMessages.length === 0) {
            messagesContainer.innerHTML = '<div class="admin-chat-loading">Belum ada pesan dalam percakapan ini.</div>';
            return;
        }
        messagesContainer.innerHTML = allMessages.map(msg => {
            const isRider = msg.sender_id === riderId;
            const senderName = msg.sender?.name ?? (isRider ? 'Rider' : 'Customer');
            const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            let contentHtml = '';
            if (msg.attachment_path) {
                if (msg.attachment_type === 'image') {
                    contentHtml += `<img src="/storage/${msg.attachment_path}" alt="attachment" style="max-width:100%; border-radius:8px; margin-bottom:0.3rem; cursor:pointer;" onclick="window.open('/storage/${msg.attachment_path}', '_blank')">`;
                } else if (msg.attachment_type === 'pdf') {
                    contentHtml += `<a href="/storage/${msg.attachment_path}" target="_blank" style="color: ${isRider ? 'white' : 'var(--accent)'}; text-decoration: underline;">📄 Lihat PDF</a>`;
                }
            }
            if (msg.body) {
                contentHtml += `<div>${escapeHtml(msg.body)}</div>`;
            }

            return `<div class="admin-chat-bubble ${isRider ? 'rider-msg' : 'customer-msg'}">
                <div class="admin-chat-bubble-sender">${escapeHtml(senderName)}</div>
                ${contentHtml}
                <div class="admin-chat-bubble-meta">${time}</div>
            </div>`;
        }).join('');
    }

    function scrollToBottom() {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 50);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    loadMessages();
</script>
@endpush
