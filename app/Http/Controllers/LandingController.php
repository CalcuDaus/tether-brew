<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartLocation;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('welcome');
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
                    'whatsapp' => $cart->user?->whatsapp,
                    'status' => $cart->status,
                    'latitude' => (float) $cart->location->latitude,
                    'longitude' => (float) $cart->location->longitude,
                    'updated_at' => $cart->location->updated_at->diffForHumans(),
                    'menu' => $cart->inventories
                        ->filter(fn($inv) => $inv->stock > 0 && $inv->product->is_active)
                        ->map(fn($inv) => [
                            'name' => $inv->product->name,
                            'price' => (float) $inv->product->price,
                            'stock' => $inv->stock,
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

