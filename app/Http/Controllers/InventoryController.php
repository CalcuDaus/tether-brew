<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with(['inventories.product', 'user'])->get();
        $products = Product::where('is_active', true)->get();
        $selectedCart = $request->get('cart_id') ? Cart::with('inventories.product')->find($request->get('cart_id')) : null;

        return view('inventories.index', compact('carts', 'products', 'selectedCart'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'stocks' => 'required|array',
            'stocks.*.product_id' => 'required|exists:products,id',
            'stocks.*.stock' => 'required|integer|min:0',
        ]);

        foreach ($validated['stocks'] as $item) {
            Inventory::updateOrCreate(
                ['cart_id' => $validated['cart_id'], 'product_id' => $item['product_id']],
                ['stock' => $item['stock']]
            );
        }

        return redirect()->route('inventories.index', ['cart_id' => $validated['cart_id']])
            ->with('success', 'Stok berhasil diperbarui!');
    }

    // Rider: update own cart stock
    public function riderUpdateStock(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'stocks' => 'required|array',
            'stocks.*.product_id' => 'required|exists:products,id',
            'stocks.*.stock' => 'required|integer|min:0',
        ]);

        foreach ($validated['stocks'] as $item) {
            Inventory::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $item['product_id']],
                ['stock' => $item['stock']]
            );
        }

        return back()->with('success', 'Stok berhasil diperbarui!');
    }
}

