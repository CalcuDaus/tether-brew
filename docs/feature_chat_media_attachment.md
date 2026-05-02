# Feature: Chat Media Attachment (Image & PDF)

## Overview

Implement file attachment support in the existing chat system between Customer (landing page) and Rider (dashboard). Users can send images (JPG, PNG, WEBP) and PDF files. Primary use case: customers sending payment proof (bukti pembayaran) to riders.

Additionally, a dedicated **"Kirim QRIS"** button must be added on the **Rider** side that instantly sends the pre-existing QRIS image (`/storage/image/tether-qris.jpeg`) without needing to pick a file.

---

## Scope of Changes

| # | File | Action |
|---|------|--------|
| 1 | `database/migrations/xxxx_add_attachment_to_messages_table.php` | **CREATE** — new migration |
| 2 | `app/Models/Message.php` | **MODIFY** — add `attachment_path` and `attachment_type` to `$fillable` |
| 3 | `app/Events/MessageSent.php` | **MODIFY** — include attachment fields in `broadcastWith()` |
| 4 | `app/Http/Controllers/ChatController.php` | **MODIFY** — update `sendMessage()` to handle file uploads |
| 5 | `resources/views/welcome.blade.php` | **MODIFY** — add attachment button + hidden file input to chat drawer |
| 6 | `resources/js/landing.js` | **MODIFY** — update `sendChatMessage()`, `renderChatMessages()`, add `sendChatAttachment()` |
| 7 | `resources/views/rider/chat-detail.blade.php` | **MODIFY** — add attachment button, QRIS button, update render & send logic |
| 8 | `resources/css/landing.css` | **MODIFY** — add styles for attachment preview and image bubbles |

---

## Step 1: Database Migration

Create a new migration file: `database/migrations/2026_05_02_000001_add_attachment_to_messages_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('body');
            $table->string('attachment_type')->nullable()->after('attachment_path'); // 'image' or 'pdf'
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_type']);
        });
    }
};
```

After creating the migration, run: `php artisan migrate`

---

## Step 2: Update Message Model

**File:** `app/Models/Message.php`

Add `attachment_path` and `attachment_type` to the `$fillable` array:

```php
protected $fillable = ['conversation_id', 'sender_id', 'body', 'is_read', 'attachment_path', 'attachment_type'];
```

No other changes needed in the model.

---

## Step 3: Update MessageSent Event

**File:** `app/Events/MessageSent.php`

Update the `broadcastWith()` method to include attachment data:

```php
public function broadcastWith(): array
{
    return [
        'id' => $this->message->id,
        'conversation_id' => $this->message->conversation_id,
        'sender_id' => $this->message->sender_id,
        'sender_name' => $this->message->sender->name,
        'body' => $this->message->body,
        'attachment_path' => $this->message->attachment_path,
        'attachment_type' => $this->message->attachment_type,
        'created_at' => $this->message->created_at->toISOString(),
    ];
}
```

---

## Step 4: Update ChatController — `sendMessage()`

**File:** `app/Http/Controllers/ChatController.php`

Replace the existing `sendMessage` method with this version that handles both text and file uploads:

```php
public function sendMessage(Request $request, Conversation $conversation)
{
    $this->authorizeConversation($conversation);

    $request->validate([
        'body' => 'nullable|string|max:1000',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120', // 5MB max
    ]);

    $attachmentPath = null;
    $attachmentType = null;

    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $ext = strtolower($file->getClientOriginalExtension());
        $attachmentType = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) ? 'image' : 'pdf';
        $attachmentPath = $file->store('chat-attachments', 'public');
    }

    // Require at least body or attachment
    if (!$request->body && !$attachmentPath) {
        return response()->json(['error' => 'Pesan atau file harus diisi.'], 422);
    }

    $message = $conversation->messages()->create([
        'sender_id' => auth()->id(),
        'body' => $request->body ?? '',
        'attachment_path' => $attachmentPath,
        'attachment_type' => $attachmentType,
    ]);

    $conversation->update(['last_message_at' => now()]);

    broadcast(new MessageSent($message->load('sender')))->toOthers();

    return response()->json(['message' => $message->load('sender')]);
}
```

### Add a new method for the Rider's "Send QRIS" shortcut:

Add this new method to `ChatController`:

```php
/**
 * Rider: Send QRIS image as a message.
 */
public function sendQris(Conversation $conversation)
{
    $this->authorizeConversation($conversation);

    $qrisPath = 'image/tether-qris.jpeg'; // relative to storage/app/public

    $message = $conversation->messages()->create([
        'sender_id' => auth()->id(),
        'body' => 'QRIS Pembayaran Tether Brew',
        'attachment_path' => $qrisPath,
        'attachment_type' => 'image',
    ]);

    $conversation->update(['last_message_at' => now()]);

    broadcast(new MessageSent($message->load('sender')))->toOthers();

    return response()->json(['message' => $message->load('sender')]);
}
```

---

## Step 5: Add Route for QRIS

**File:** `routes/web.php`

Inside the **Rider Chat Routes** group (line ~150), add:

```php
Route::post('/{conversation}/send-qris', [ChatController::class, 'sendQris'])->name('qris');
```

So the rider chat group becomes:

```php
Route::middleware(['auth', 'role:rider'])->prefix('rider/chat')->name('rider.chat.')->group(function () {
    Route::get('/', [ChatController::class, 'riderConversations'])->name('index');
    Route::get('/{conversation}', [ChatController::class, 'riderChat'])->name('show');
    Route::get('/{conversation}/messages', [ChatController::class, 'getMessages'])->name('messages');
    Route::post('/{conversation}/send', [ChatController::class, 'sendMessage'])->name('send');
    Route::post('/{conversation}/send-qris', [ChatController::class, 'sendQris'])->name('qris');
});
```

---

## Step 6: Update Customer Chat Drawer (Landing Page)

### 6a. HTML — `resources/views/welcome.blade.php`

Find the chat input wrapper (around line 578):

```html
<div class="chat-input-wrapper">
    <input id="chat-input" type="text" placeholder="Ketik pesan..." maxlength="1000" autocomplete="off">
    <button id="chat-send-btn" onclick="sendChatMessage()">
        <svg ...>...</svg>
    </button>
</div>
```

Replace it with:

```html
<div class="chat-input-wrapper">
    <input type="file" id="chat-file-input" accept=".jpg,.jpeg,.png,.webp,.pdf" style="display:none;" onchange="sendChatAttachment()">
    <button class="chat-attach-btn" onclick="document.getElementById('chat-file-input').click()" title="Lampirkan file">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path></svg>
    </button>
    <input id="chat-input" type="text" placeholder="Ketik pesan..." maxlength="1000" autocomplete="off">
    <button id="chat-send-btn" onclick="sendChatMessage()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
    </button>
</div>
```

### 6b. JavaScript — `resources/js/landing.js`

#### Update `renderChatMessages()` (around line 781)

Replace the message rendering inside `.map()` to handle attachments:

```javascript
container.innerHTML = chatMessages.map(msg => {
    const isSent = msg.sender_id === customerId;
    const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

    let contentHtml = '';
    if (msg.attachment_path) {
        if (msg.attachment_type === 'image') {
            contentHtml += `<img src="/storage/${msg.attachment_path}" alt="attachment" class="chat-attachment-img" onclick="window.open('/storage/${msg.attachment_path}', '_blank')">`;
        } else if (msg.attachment_type === 'pdf') {
            contentHtml += `<a href="/storage/${msg.attachment_path}" target="_blank" class="chat-attachment-pdf">📄 Lihat PDF</a>`;
        }
    }
    if (msg.body) {
        contentHtml += `<div>${escapeHtmlChat(msg.body)}</div>`;
    }

    return `<div class="chat-bubble ${isSent ? 'sent' : 'received'}">
        ${contentHtml}
        <div class="chat-bubble-time">${time}</div>
    </div>`;
}).join('');
```

#### Add new function `sendChatAttachment()` (place it after `sendChatMessage()`)

```javascript
async function sendChatAttachment() {
    if (!currentConversation) return;

    const fileInput = document.getElementById('chat-file-input');
    const file = fileInput.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('attachment', file);
    formData.append('body', ''); // optional text, leave empty

    try {
        const res = await fetch(`/chat/${currentConversation.id}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData, // NOTE: Do NOT set Content-Type header, browser sets it with boundary
        });

        if (!res.ok) throw new Error('Upload failed');

        const data = await res.json();
        chatMessages.push(data.message);
        renderChatMessages();
        scrollChatToBottom();
    } catch (err) {
        console.error('Attachment send failed:', err);
    }

    fileInput.value = ''; // reset input
}
```

#### Expose it globally (at the bottom where `window.xxx = xxx` are):

```javascript
window.sendChatAttachment = sendChatAttachment;
```

#### Update the WebSocket listener and polling to include attachment fields

In the `subscribeToChatChannel` function, where `chatMessages.push({...})` is called inside `.listen('MessageSent', ...)`, add:

```javascript
attachment_path: e.attachment_path,
attachment_type: e.attachment_type,
```

So it becomes:

```javascript
chatMessages.push({
    id: e.id,
    conversation_id: e.conversation_id,
    sender_id: e.sender_id,
    body: e.body,
    attachment_path: e.attachment_path,
    attachment_type: e.attachment_type,
    created_at: e.created_at,
    sender: { name: e.sender_name },
});
```

---

## Step 7: Update Rider Chat Detail Page

**File:** `resources/views/rider/chat-detail.blade.php`

### 7a. Update HTML input area (around line 192)

Replace:

```html
<div class="chat-input-area">
    <input type="text" id="chat-input" placeholder="Ketik pesan..." maxlength="1000" autocomplete="off">
    <button class="chat-send-btn" id="chat-send-btn" onclick="sendRiderMessage()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
        Kirim
    </button>
</div>
```

With:

```html
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
```

### 7b. Update the JavaScript `<script>` block in `chat-detail.blade.php`

#### Update `renderMessages()` to display attachments:

```javascript
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
                contentHtml += `<a href="/storage/${msg.attachment_path}" target="_blank" style="color: ${isSent ? 'white' : 'var(--accent-blue, #42a5f5)'}; text-decoration: underline;">📄 Lihat PDF</a>`;
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
```

#### Add `sendRiderAttachment()` function:

```javascript
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
```

#### Add `sendQrisMessage()` function:

```javascript
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
```

#### Update the Echo `.listen('MessageSent', ...)` to include attachment fields:

Add `attachment_path` and `attachment_type` to the pushed object, same as in landing.js (see Step 6).

---

## Step 8: CSS for Attachment Bubbles

**File:** `resources/css/landing.css`

Append at the end:

```css
/* Chat Attachment Styles */
.chat-attach-btn {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border: 1px solid var(--border-color, #2a2a3e);
    border-radius: 12px;
    background: transparent;
    color: var(--text-secondary, #a0aec0);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}
.chat-attach-btn:hover {
    border-color: #3b82f6;
    color: #3b82f6;
}
.chat-attachment-img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    margin-bottom: 0.3rem;
    cursor: pointer;
    object-fit: cover;
    transition: opacity 0.2s;
}
.chat-attachment-img:hover {
    opacity: 0.85;
}
.chat-attachment-pdf {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.4rem 0.7rem;
    border-radius: 8px;
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 500;
    margin-bottom: 0.3rem;
    transition: background 0.2s;
}
.chat-attachment-pdf:hover {
    background: rgba(59, 130, 246, 0.2);
}
```

---

## Step 9: Verify

After implementing all changes:

1. Run `php artisan migrate` to add the new columns.
2. Ensure `php artisan storage:link` has been run (so `/storage/` symlink exists in `public/`).
3. Reload the page. Test sending an image from the **Customer** side and verify it renders in both the customer and rider chat.
4. On the **Rider** side, click the green **QRIS** button and verify the QRIS image (`/storage/image/tether-qris.jpeg`) is sent and rendered in both sides.
5. Test sending a PDF file and verify the "📄 Lihat PDF" link appears and opens the file in a new tab.

---

## Important Notes

- The `sendMessage()` controller now receives `FormData` instead of JSON when an attachment is present. **Do NOT set `Content-Type: application/json`** header in the JavaScript `fetch` call when sending FormData — the browser will automatically set `Content-Type: multipart/form-data` with the correct boundary.
- The QRIS image path `image/tether-qris.jpeg` is hardcoded relative to `storage/app/public/`. This file already exists at `storage/app/public/image/tether-qris.jpeg`.
- Max file size is 5MB (`max:5120` in validation). Adjust in the controller if needed.
- The `body` field validation changed from `required|string` to `nullable|string` to allow image-only messages.
