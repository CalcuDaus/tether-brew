<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // POS page for rider
    public function posIndex(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->with(['inventories.product'])
            ->firstOrFail();

        $products = [];
        foreach ($cart->inventories as $inv) {
            if ($inv->stock > 0 && $inv->product->is_active) {
                $products[] = [
                    'id' => $inv->product->id,
                    'name' => $inv->product->name,
                    'price' => (float) $inv->product->price,
                    'stock' => $inv->stock,
                    'category' => $inv->product->category,
                ];
            }
        }

        return view('rider.pos', compact('cart', 'products'));
    }

    // Process POS transaction
    public function posStore(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris',
            'notes' => 'nullable|string',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();

        return DB::transaction(function () use ($validated, $cart, $request) {
            $totalPrice = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['qty'];
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                // Reduce stock
                $inventory = Inventory::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($inventory) {
                    $inventory->decrement('stock', $item['qty']);
                }
            }

            $transaction = Transaction::create([
                'cart_id' => $cart->id,
                'user_id' => $request->user()->id,
                'total_price' => $totalPrice,
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $itemData) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    ...$itemData,
                ]);
            }

            return redirect()->route('rider.pos')->with('success', 'Transaksi berhasil! Total: Rp ' . number_format($totalPrice, 0, ',', '.'));
        });
    }

    // Rider: transaction history
    public function riderHistory(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->first();

        $transactions = collect();
        if ($cart) {
            $transactions = Transaction::where('cart_id', $cart->id)
                ->with('items.product')
                ->latest()
                ->paginate(15);
        }

        return view('rider.transactions', compact('transactions'));
    }

    // Admin: all transactions
    public function index(Request $request)
    {
        $query = Transaction::with(['cart', 'user', 'items.product'])->latest();

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                // Remove '#' if user typed it for ID
                $cleanSearch = str_replace('#', '', $search);
                if (is_numeric($cleanSearch)) {
                    $q->where('id', $cleanSearch);
                }
                
                $q->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('cart', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }
}

