<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartLocation;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $query = Cart::with(['user', 'location'])->latest();
        
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $carts = $query->paginate(10)->withQueryString();
        return view('carts.index', compact('carts'));
    }

    public function create()
    {
        $riders = User::where('role', 'rider')->get();
        return view('carts.create', compact('riders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive,closed',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $cart = Cart::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            CartLocation::create([
                'cart_id' => $cart->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);
        }

        return redirect()->route('carts.index')->with('success', 'Gerobak berhasil ditambahkan!');
    }

    public function edit(Cart $cart)
    {
        $cart->load('location');
        $riders = User::where('role', 'rider')->get();
        return view('carts.edit', compact('cart', 'riders'));
    }

    public function update(Request $request, Cart $cart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive,closed',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $cart->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            CartLocation::updateOrCreate(
                ['cart_id' => $cart->id],
                ['latitude' => $validated['latitude'], 'longitude' => $validated['longitude']]
            );
        }

        return redirect()->route('carts.index')->with('success', 'Gerobak berhasil diperbarui!');
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return redirect()->route('carts.index')->with('success', 'Gerobak berhasil dihapus!');
    }

    public function toggleStatus(Cart $cart)
    {
        $newStatus = $cart->status === 'active' ? 'inactive' : 'active';
        $cart->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Status gerobak berhasil diubah ke ' . ucfirst($newStatus),
        ]);
    }

    // Rider: Update location (form submit)
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();

        CartLocation::updateOrCreate(
            ['cart_id' => $cart->id],
            ['latitude' => $validated['latitude'], 'longitude' => $validated['longitude']]
        );

        return back()->with('success', 'Lokasi berhasil diperbarui!');
    }

    // Admin/Owner: Get all carts with locations for tracking map
    public function mapData()
    {
        $carts = Cart::with(['location', 'user'])
            ->where('status', 'active')
            ->get()
            ->filter(fn($cart) => $cart->location !== null)
            ->map(function ($cart) {
                return [
                    'id' => $cart->id,
                    'name' => $cart->name,
                    'rider' => $cart->user?->name ?? 'Tidak ada rider',
                    'status' => $cart->status,
                    'latitude' => (float) $cart->location->latitude,
                    'longitude' => (float) $cart->location->longitude,
                    'updated_at' => $cart->location->updated_at->diffForHumans(),
                    'edit_url' => route('carts.edit', $cart),
                ];
            })
            ->values();

        return response()->json($carts);
    }

    // Rider: Update location (AJAX live)
    public function updateLocationLive(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Cart not found for this user'], 404);
        }

        CartLocation::updateOrCreate(
            ['cart_id' => $cart->id],
            [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'updated_at' => now()
            ]
        );

        return response()->json(['success' => true]);
    }
}

