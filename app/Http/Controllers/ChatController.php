<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Customer: Start or resume a conversation with a rider.
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'rider_id' => 'required|exists:users,id',
            'cart_id' => 'nullable|exists:carts,id',
        ]);

        $conversation = Conversation::firstOrCreate(
            [
                'customer_id' => auth()->id(),
                'rider_id' => $request->rider_id,
                'cart_id' => $request->cart_id,
            ],
            ['status' => 'active']
        );

        return response()->json([
            'conversation' => $conversation->load(['rider', 'cart']),
        ]);
    }

    /**
     * Shared: Send a message in a conversation.
     */
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

    /**
     * Shared: Get paginated messages for a conversation.
     */
    public function getMessages(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $messages = $conversation->messages()
            ->with('sender:id,name,role')
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json($messages);
    }

    /**
     * Shared: Mark messages from the other user as read.
     */
    public function markAsRead(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Rider: List all conversations for the rider.
     */
    public function riderConversations()
    {
        $conversations = Conversation::where('rider_id', auth()->id())
            ->with(['customer:id,name,phone', 'cart:id,name', 'latestMessage'])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('sender_id', '!=', auth()->id())->where('is_read', false);
            }])
            ->orderByDesc('last_message_at')
            ->get();

        return view('rider.chat', compact('conversations'));
    }

    /**
     * Rider: JSON list of conversations (for polling).
     */
    public function riderConversationsJson()
    {
        $conversations = Conversation::where('rider_id', auth()->id())
            ->with(['customer:id,name,phone', 'cart:id,name', 'latestMessage'])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('sender_id', '!=', auth()->id())->where('is_read', false);
            }])
            ->orderByDesc('last_message_at')
            ->get();

        return response()->json(['conversations' => $conversations]);
    }

    /**
     * Rider: Show a specific chat conversation.
     */
    public function riderChat(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $conversation->load(['customer', 'cart']);
        return view('rider.chat-detail', compact('conversation'));
    }

    /**
     * Authorize that the current user is a participant in the conversation.
     */
    private function authorizeConversation(Conversation $conversation): void
    {
        $userId = auth()->id();
        if ($conversation->customer_id !== $userId && $conversation->rider_id !== $userId) {
            abort(403);
        }
    }
}
