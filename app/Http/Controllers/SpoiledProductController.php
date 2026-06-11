<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SpoiledProduct;

class SpoiledProductController extends Controller
{
    public function index(Request $request)
    {
        $branchId = activeBranchId();
        $query = SpoiledProduct::forBranch($branchId)
            ->with(['user', 'items.product'])
            ->orderBy('date', 'desc');

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        $spoiledProducts = $query->paginate(10)->withQueryString();

        return view('admin.spoiled_products.index', compact('spoiledProducts'));
    }

    public function create()
    {
        $products = \App\Models\Product::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.spoiled_products.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array',
        ]);

        $branchId = activeBranchId();

        // Cek jika sudah ada input di tanggal ini
        $existing = SpoiledProduct::forBranch($branchId)
            ->whereDate('date', $request->date)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data produk basi untuk tanggal tersebut sudah ada! Silakan edit data yang sudah ada.');
        }

        DB::transaction(function () use ($request, $branchId) {
            $spoiled = SpoiledProduct::create([
                'date' => $request->date,
                'branch_id' => $branchId,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->items as $productId => $data) {
                $qty = (int) ($data['quantity'] ?? 0);
                if ($qty > 0) {
                    $spoiled->items()->create([
                        'product_id' => $productId,
                        'quantity' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('admin.spoiled_products.index')->with('success', 'Data produk basi berhasil disimpan.');
    }

    public function edit(SpoiledProduct $spoiledProduct)
    {
        $products = \App\Models\Product::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.spoiled_products.edit', compact('spoiledProduct', 'products'));
    }

    public function update(Request $request, SpoiledProduct $spoiledProduct)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array',
        ]);

        $branchId = activeBranchId();
        if ($spoiledProduct->branch_id !== $branchId) abort(403);

        // Cek duplikasi tanggal
        $existing = SpoiledProduct::forBranch($branchId)
            ->whereDate('date', $request->date)
            ->where('id', '!=', $spoiledProduct->id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data produk basi untuk tanggal tersebut sudah ada!');
        }

        DB::transaction(function () use ($request, $spoiledProduct) {
            $spoiledProduct->update([
                'date' => $request->date,
            ]);

            $spoiledProduct->items()->delete();

            foreach ($request->items as $productId => $data) {
                $qty = (int) ($data['quantity'] ?? 0);
                if ($qty > 0) {
                    $spoiledProduct->items()->create([
                        'product_id' => $productId,
                        'quantity' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('admin.spoiled_products.index')->with('success', 'Data produk basi berhasil diperbarui.');
    }

    public function destroy(SpoiledProduct $spoiledProduct)
    {
        if ($spoiledProduct->branch_id !== activeBranchId()) abort(403);
        $spoiledProduct->delete();
        return redirect()->route('admin.spoiled_products.index')->with('success', 'Data produk basi berhasil dihapus.');
    }
}
