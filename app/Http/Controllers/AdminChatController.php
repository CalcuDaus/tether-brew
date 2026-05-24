<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    /**
     * Tampilkan semua percakapan rider-customer.
     */
    public function index(Request $request)
    {
        $conversations = Conversation::with(['customer:id,name,phone', 'rider:id,name', 'cart:id,name', 'latestMessage'])
            ->withCount('messages')
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                      ->orWhereHas('rider', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            })
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('admin.chats.index', compact('conversations'));
    }

    /**
     * Tampilkan detail percakapan (read-only).
     */
    public function show(Conversation $conversation)
    {
        $conversation->load(['customer', 'rider', 'cart']);
        return view('admin.chats.show', compact('conversation'));
    }

    /**
     * API: Ambil pesan percakapan (JSON, paginated).
     */
    public function getMessages(Conversation $conversation)
    {
        $messages = $conversation->messages()
            ->with('sender:id,name,role')
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json($messages);
    }
}
