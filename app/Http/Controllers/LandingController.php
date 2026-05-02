<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\Cart;
use App\Models\CartLocation;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // 3 artikel terbaru yang sudah dipublish
        $artikels = Artikel::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $myTransactions = collect();
        $myConversations = collect();

        if (auth()->check() && auth()->user()->isCustomer()) {
            $myTransactions = \App\Models\Transaction::where('user_id', auth()->id())
                ->with('cart')
                ->latest()
                ->take(10)
                ->get();

            $myConversations = \App\Models\Conversation::where('customer_id', auth()->id())
                ->with(['rider', 'cart'])
                ->withCount(['messages as unread_count' => function ($query) {
                    $query->where('sender_id', '!=', auth()->id())->where('is_read', false);
                }])
                ->orderByDesc('last_message_at')
                ->get();
        }

        return view('welcome', compact('artikels', 'myTransactions', 'myConversations'));
    }

    // API endpoint for map data (returns JSON)
    public function cartsMapData()
    {
        $carts = Cart::where('status', 'active')
            ->with(['location', 'user', 'inventories.product'])
            ->get()
            ->filter(fn($cart) => $cart->location !== null)
            ->map(function ($cart) {
                return [
                    'id' => $cart->id,
                    'name' => $cart->name,
                    'description' => $cart->description,
                    'rider' => $cart->user?->name ?? 'Tidak ada rider',
                    'rider_id' => $cart->user_id,
                    'whatsapp' => $cart->user?->whatsapp,
                    'status' => $cart->status,
                    'latitude' => (float) $cart->location->latitude,
                    'longitude' => (float) $cart->location->longitude,
                    'updated_at' => $cart->location->updated_at->diffForHumans(),
                    'menu' => $cart->inventories
                        ->filter(fn($inv) => $inv->product->is_active)
                        ->map(fn($inv) => [
                            'name' => $inv->product->name,
                            'price' => (float) $inv->product->price,
                            'stock' => 99,
                            'category' => $inv->product->category,
                            'image' => $inv->product->image,
                        ])
                        ->values(),
                ];
            })
            ->values();

        return response()->json($carts);
    }
}
