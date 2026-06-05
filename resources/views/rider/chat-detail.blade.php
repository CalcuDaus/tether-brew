@extends('layouts.app')

@section('title', 'Chat: ' . ($conversation->customer->name ?? 'Customer'))

@push('styles')
<style>
    .chat-detail-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        height: calc(100dvh - 120px);
        max-width: 720px;
    }
    .chat-detail-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.8rem 1rem;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        margin-bottom: 0.8rem;
    }
    .chat-detail-back {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--bg);
        border: 1px solid var(--border);
        color: var(--text);
        text-decoration: none;
        transition: all 0.2s;
    }
    .chat-detail-back:hover {
        border-color: var(--accent-green, #22c55e);
        color: var(--accent-green, #22c55e);
    }
    .chat-detail-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .chat-detail-info h3 {
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
    }
    .chat-detail-info p {
        font-size: 0.75rem;
        opacity: 0.5;
        margin: 0;
    }
    .chat-messages-area {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        padding: 1rem;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        margin-bottom: 0.8rem;
    }
    .chat-messages-area::-webkit-scrollbar {
        width: 4px;
    }
    .chat-messages-area::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 4px;
    }
    .chat-bubble {
        max-width: 75%;
        padding: 0.6rem 0.9rem;
        border-radius: 16px;
        font-size: 0.85rem;
        line-height: 1.5;
        animation: bubbleIn 0.2s ease-out;
    }
    .chat-bubble.sent {
        background: linear-gradient(135deg, #914a30, #6d3824);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
    }
    .chat-bubble.received {
        background: rgba(128, 128, 128, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(128, 128, 128, 0.2);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }
    .chat-bubble-meta {
        font-size: 0.65rem;
        opacity: 0.6;
        margin-top: 0.2rem;
    }
    .chat-input-area {
        display: flex;
        gap: 0.5rem;
        padding: 0.8rem;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
    }
    .chat-input-area input {
        flex: 1;
        padding: 0.65rem 1rem;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--bg);
        color: var(--text);
        font-size: 0.85rem;
        font-family: inherit;
    }
    .chat-input-area input:focus {
        outline: none;
        border-color: var(--accent-green, #22c55e);
    }
    .chat-send-btn {
        padding: 0.65rem 1.2rem;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        font-family: inherit;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .chat-send-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(34,197,94,0.25);
    }
    .chat-send-btn:active {
        transform: translateY(0);
    }
    .chat-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    .chat-date-divider {
        text-align: center;
        font-size: 0.7rem;
        opacity: 0.4;
        padding: 0.5rem 0;
    }
    .chat-loading {
        text-align: center;
        padding: 2rem;
        opacity: 0.5;
        font-size: 0.85rem;
    }
    @keyframes bubbleIn {
        from { opacity: 0; transform: translateY(8px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Responsive adjustments for mobile */
    @media (max-width: 480px) {
        .chat-detail-container {
            height: calc(100dvh - 190px);
            flex-wrap: nowrap;
        }
        .chat-input-area {
            flex-wrap: wrap;
        }
        .chat-input-area input[type="text"] {
            flex: 1 1 100%;
            order: -1; /* Move input to the top row */
        }
        .chat-send-btn, .chat-qris-btn, .chat-attach-btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="chat-detail-container">
    <div class="chat-detail-header">
        <a href="{{ route('rider.chat.index') }}" class="chat-detail-back">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </a>
        <div class="chat-detail-avatar">{{ strtoupper(substr($conversation->customer->name ?? '?', 0, 1)) }}</div>
        <div class="chat-detail-info">
            <h3>{{ $conversation->customer->name ?? 'Customer' }}</h3>
            <p>{{ $conversation->customer->phone ?? '' }}</p>
        </div>
    </div>

    <div class="chat-messages-area" id="chat-messages">
        <div class="chat-loading" id="chat-loading">Memuat pesan...</div>
    </div>

    <div class="chat-input-area">
        <input type="file" id="rider-file-input" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none;" onchange="sendRiderAttachment()">
        <button class="chat-attach-btn" onclick="document.getElementById('rider-file-input').click()" title="Lampirkan file" style="padding: 0.65rem; border: 1px solid var(--border); border-radius: 12px; background: var(--bg); color: var(--text); cursor: pointer; display: flex; align-items: center;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path></svg>
        </button>
        <input type="text" id="chat-input" placeholder="Ketik pesan..." maxlength="1000" autocomplete="off">
        <button class="chat-send-btn" id="chat-send-btn" onclick="sendRiderMessage()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            Kirim
        </button>
        <button class="chat-qris-btn" onclick="sendQrisMessage()" title="Kirim QRIS" style="padding: 0.65rem 1rem; border: none; border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 600; font-size: 0.85rem; font-family: inherit; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; transition: transform 0.15s, box-shadow 0.2s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
            QRIS
        </button>
    </div>
</div>

@push('scripts')
<script>
    const conversationId = {{ $conversation->id }};
    const currentUserId = {{ auth()->id() }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const messagesContainer = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('chat-send-btn');

    let allMessages = [];
    let riderChatPollingTimer = null;

    // Load messages
    async function loadMessages() {
        try {
            const res = await fetch(`/rider/chat/${conversationId}/messages?_t=${Date.now()}`, {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
            });
            const data = await res.json();
            allMessages = (data.data || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            renderMessages();
            scrollToBottom();

            // Mark as read
            fetch(`/chat/${conversationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            });
        } catch (err) {
            document.getElementById('chat-loading').textContent = 'Gagal memuat pesan.';
        }
    }

    // Polling: fetch new messages every 3 seconds
    function startRiderChatPolling() {
        if (riderChatPollingTimer) clearInterval(riderChatPollingTimer);
        riderChatPollingTimer = setInterval(async () => {
            try {
                const res = await fetch(`/rider/chat/${conversationId}/messages?_t=${Date.now()}`, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                });
                const data = await res.json();
                const freshMessages = (data.data || []).sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                // Only update if there are new messages
                if (freshMessages.length !== allMessages.length) {
                    allMessages = freshMessages;
                    renderMessages();
                    scrollToBottom();

                    // Mark as read
                    fetch(`/chat/${conversationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    });
                }
            } catch (err) {
                // Silently ignore polling errors
            }
        }, 3000);
    }

    function renderMessages() {
        if (allMessages.length === 0) {
            messagesContainer.innerHTML = '<div class="chat-loading">Belum ada pesan. Mulai percakapan!</div>';
            return;
        }
        messagesContainer.innerHTML = allMessages.map(msg => {
            const isSent = msg.sender_id === currentUserId;
            const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            let contentHtml = '';
            if (msg.attachment_path) {
                if (msg.attachment_type === 'image') {
                    contentHtml += `<img src="/storage/${msg.attachment_path}" alt="attachment" style="max-width:100%; border-radius:8px; margin-bottom:0.3rem; cursor:pointer;" onclick="window.open('/storage/${msg.attachment_path}', '_blank')">`;
                } else if (msg.attachment_type === 'pdf') {
                    contentHtml += `<a href="/storage/${msg.attachment_path}" target="_blank" style="color: ${isSent ? 'white' : 'var(--accent-green, #22c55e)'}; text-decoration: underline;">ðŸ“„ Lihat PDF</a>`;
                }
            }
            if (msg.body) {
                contentHtml += `<div>${escapeHtml(msg.body)}</div>`;
            }

            return `<div class="chat-bubble ${isSent ? 'sent' : 'received'}">
                ${contentHtml}
                <div class="chat-bubble-meta">${time}</div>
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

    async function sendRiderMessage() {
        const body = chatInput.value.trim();
        if (!body) return;

        sendBtn.disabled = true;
        chatInput.value = '';

        try {
            const res = await fetch(`/rider/chat/${conversationId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ body }),
            });
            const data = await res.json();
            allMessages.push(data.message);
            renderMessages();
            scrollToBottom();
        } catch (err) {
            chatInput.value = body;
        }
        sendBtn.disabled = false;
        chatInput.focus();
    }

    async function sendRiderAttachment() {
        const fileInput = document.getElementById('rider-file-input');
        const file = fileInput.files[0];
        if (!file) return;

        sendBtn.disabled = true;
        const formData = new FormData();
        formData.append('attachment', file);
        formData.append('body', '');

        try {
            const res = await fetch(`/rider/chat/${conversationId}/send`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });
            const data = await res.json();
            allMessages.push(data.message);
            renderMessages();
            scrollToBottom();
        } catch (err) {
            console.error('Attachment send failed:', err);
        }
        sendBtn.disabled = false;
        fileInput.value = '';
    }

    async function sendQrisMessage() {
        try {
            const res = await fetch(`/rider/chat/${conversationId}/send-qris`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            allMessages.push(data.message);
            renderMessages();
            scrollToBottom();
        } catch (err) {
            console.error('QRIS send failed:', err);
        }
    }

    // Enter to send
    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendRiderMessage();
        }
    });

    // Listen for realtime messages via Echo (best-effort)
    if (typeof window.Echo !== 'undefined') {
        window.Echo.private(`conversation.${conversationId}`)
            .listen('MessageSent', (e) => {
                // Deduplicate (polling may have already added it)
                if (allMessages.some(m => m.id === e.id)) return;

                allMessages.push({
                    id: e.id,
                    conversation_id: e.conversation_id,
                    sender_id: e.sender_id,
                    body: e.body,
                    attachment_path: e.attachment_path,
                    attachment_type: e.attachment_type,
                    created_at: e.created_at,
                    sender: { name: e.sender_name },
                });
                renderMessages();
                scrollToBottom();

                // Mark as read
                fetch(`/chat/${conversationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
            });
    }

    // Initial load + start polling
    loadMessages();
    startRiderChatPolling();

    // Stop polling when leaving the page
    window.addEventListener('beforeunload', () => {
        if (riderChatPollingTimer) clearInterval(riderChatPollingTimer);
    });
</script>
@endpush
@endsection
