@extends('layouts.customer')

@section('title', 'Chats - Tether Brew')

@push('styles')
<style>
    .chats-header {
        padding: 20px;
        background: white;
        position: sticky;
        top: 0;
        z-index: 40;
        border-bottom: 1px solid #f1f5f9;
    }
    .chats-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .conversations-list {
        display: flex;
        flex-direction: column;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        background: white;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        color: inherit;
        gap: 15px;
    }

    .conversation-item:active {
        background: #f8fafc;
    }

    .chat-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5c2a, #6b4420);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .chat-info {
        flex: 1;
        overflow: hidden;
    }

    .chat-name {
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-preview {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .unread-badge {
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
    }
</style>
@endpush

@section('content')
    <div class="chats-header">
        <h1 class="chats-title">Chat Rider</h1>
    </div>

    <div class="conversations-list">
        @forelse($conversations as $conv)
            <a href="javascript:void(0)" class="conversation-item" onclick="openChat({{ $conv->id }}, '{{ $conv->rider->name }}')">
                <div class="chat-avatar">
                    {{ strtoupper(substr($conv->rider->name, 0, 1)) }}
                </div>
                <div class="chat-info">
                    <h3 class="chat-name">{{ $conv->rider->name }} ({{ $conv->cart->name ?? 'Gerobak' }})</h3>
                    <p class="chat-preview">Chat untuk memesan kopi...</p>
                </div>
                <div class="chat-meta">
                    <span>{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '' }}</span>
                    @if($conv->unread_count > 0)
                        <span class="unread-badge">{{ $conv->unread_count }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                Belum ada percakapan. Temukan gerobak di Beranda atau Menu untuk mulai memesan!
            </div>
        @endforelse
    </div>

    <!-- Chat Drawer -->
    <div id="chat-drawer" class="chat-drawer" style="display:none; position: fixed; inset: 0; background: white; z-index: 100; flex-direction: column;">
        <div class="chat-drawer-header" style="display:flex; align-items:center; padding: 15px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <button class="chat-drawer-close" onclick="closeChat()" style="background: none; border: none; font-size: 1.5rem; color: #64748b; margin-right: 15px;">â†</button>
            <div class="chat-drawer-header-info" style="display:flex; align-items:center; gap: 10px;">
                <div class="chat-drawer-avatar" id="chat-rider-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: #8b5c2a; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">R</div>
                <div>
                    <div class="chat-drawer-rider-name" id="chat-rider-name" style="font-weight: 600; color: #1e293b;">Rider</div>
                    <div class="chat-drawer-status" style="font-size: 0.8rem; color: #10b981;">Online</div>
                </div>
            </div>
        </div>
        <div id="chat-messages" class="chat-messages" style="flex: 1; overflow-y: auto; padding: 15px; display: flex; flex-direction: column; gap: 10px; background: #f1f5f9;">
        </div>
        <div class="chat-input-wrapper" style="padding: 15px; background: white; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;">
            <input id="chat-input" type="text" placeholder="Ketik pesan..." style="flex: 1; padding: 10px 15px; border-radius: 20px; border: 1px solid #cbd5e1; outline: none;" onkeypress="if(event.key === 'Enter') sendChatMessage()">
            <button id="chat-send-btn" onclick="sendChatMessage()" style="background: #8b5c2a; color: white; border: none; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>

    <style>
        .chat-bubble { max-width: 75%; padding: 10px 15px; border-radius: 12px; font-size: 0.95rem; position: relative; }
        .chat-bubble.sent { background: #8b5c2a; color: white; align-self: flex-end; border-bottom-right-radius: 4px; }
        .chat-bubble.received { background: white; color: #1e293b; align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid #e2e8f0; }
        .chat-bubble-time { font-size: 0.65rem; text-align: right; margin-top: 4px; opacity: 0.7; }
        .chat-empty { text-align: center; color: #64748b; margin-top: 20px; font-size: 0.9rem; }
    </style>

    <script>
        let currentConversationId = null;
        let chatPollingTimer = null;
        let chatMessages = [];
        const customerId = {{ auth()->id() }};

        async function openChat(cartId, riderId, riderName) {
            document.getElementById('chat-rider-name').textContent = riderName;
            document.getElementById('chat-rider-avatar').textContent = riderName.charAt(0).toUpperCase();
            document.getElementById('chat-drawer').style.display = 'flex';
            document.getElementById('chat-messages').innerHTML = '<div class="chat-empty">Memuat pesan...</div>';

            try {
                const res = await fetch('/chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ rider_id: riderId, cart_id: cartId }),
                });

                if (!res.ok) throw new Error('Gagal memulai chat');
                const data = await res.json();
                currentConversationId = data.conversation.id;

                await loadChatMessages();
                startChatPolling();
            } catch (err) {
                console.error(err);
                document.getElementById('chat-messages').innerHTML = '<div class="chat-empty">Gagal memuat chat.</div>';
            }
        }

        function closeChat() {
            document.getElementById('chat-drawer').style.display = 'none';
            if (chatPollingTimer) clearInterval(chatPollingTimer);
            currentConversationId = null;
        }

        async function loadChatMessages() {
            if (!currentConversationId) return;
            try {
                const res = await fetch(`/chat/${currentConversationId}/messages?_t=${Date.now()}`);
                const data = await res.json();
                chatMessages = (data.data || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                renderMessages();
                
                fetch(`/chat/${currentConversationId}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                });
            } catch (err) {}
        }

        function startChatPolling() {
            if (chatPollingTimer) clearInterval(chatPollingTimer);
            chatPollingTimer = setInterval(async () => {
                if (!currentConversationId) return;
                try {
                    const res = await fetch(`/chat/${currentConversationId}/messages?_t=${Date.now()}`);
                    const data = await res.json();
                    const freshMessages = (data.data || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    
                    if (freshMessages.length !== chatMessages.length) {
                        chatMessages = freshMessages;
                        renderMessages();
                        fetch(`/chat/${currentConversationId}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }});
                    }
                } catch (err) {}
            }, 3000);
        }

        function renderMessages() {
            const container = document.getElementById('chat-messages');
            if (chatMessages.length === 0) {
                container.innerHTML = '<div class="chat-empty">Belum ada pesan. Ketik pesan di bawah!</div>';
                return;
            }

            container.innerHTML = chatMessages.map(msg => {
                const isSent = msg.sender_id === customerId;
                const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                return `<div class="chat-bubble ${isSent ? 'sent' : 'received'}">
                    <div>${escapeHtml(msg.body)}</div>
                    <div class="chat-bubble-time">${time}</div>
                </div>`;
            }).join('');
            
            container.scrollTop = container.scrollHeight;
        }

        async function sendChatMessage() {
            if (!currentConversationId) return;
            const input = document.getElementById('chat-input');
            const body = input.value.trim();
            if (!body) return;

            input.value = '';
            
            // Optimistic rendering
            const tempMsg = { sender_id: customerId, body: body, created_at: new Date().toISOString() };
            chatMessages.push(tempMsg);
            renderMessages();

            try {
                await fetch(`/chat/${currentConversationId}/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ body: body }),
                });
                loadChatMessages();
            } catch (err) {
                console.error(err);
            }
        }

        function escapeHtml(unsafe) {
            return (unsafe || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }
    </script>
@endsection
