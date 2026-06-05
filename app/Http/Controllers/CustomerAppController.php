<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Conversation;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CustomerAppController extends Controller
{
    private $quotes = [
        "Kopi pertama hari ini adalah pelukan dalam cangkir.",
        "Setiap cangkir kopi adalah cerita yang menunggu untuk dicicipi.",
        "Bangun, minum kopi, dan jadikan hari ini luar biasa.",
        "Tidak ada yang tidak mungkin jika kamu punya cukup kopi.",
        "Kopi yang baik, teman yang baik, waktu yang baik.",
        "Awali pagimu dengan senyuman dan secangkir kopi hangat.",
        "Biarkan aroma kopi menginspirasi langkahmu hari ini."
    ];

    public function dashboard()
    {
        // Get a daily quote based on the day of the year so it stays consistent for the day
        $quoteIndex = date('z') % count($this->quotes);
        $dailyQuote = $this->quotes[$quoteIndex];

        return view('customer.dashboard.index', compact('dailyQuote'));
    }

    public function menu()
    {
        $products = Product::where('is_active', true)->get();
        return view('customer.menu.index', compact('products'));
    }

    public function chats()
    {
        $conversations = Conversation::where('customer_id', auth()->id())
            ->with(['rider', 'cart'])
            ->withCount(['messages as unread_count' => function ($query) {
                $query->where('sender_id', '!=', auth()->id())->where('is_read', false);
            }])
            ->orderByDesc('last_message_at')
            ->get();

        return view('customer.chats.index', compact('conversations'));
    }

    public function me()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with('cart')
            ->latest()
            ->take(5)
            ->get();

        return view('customer.profile.index', compact('transactions'));
    }
}
