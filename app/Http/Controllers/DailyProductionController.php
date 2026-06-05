<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DailyProduction;

class DailyProductionController extends Controller
{
    public function index(Request $request)
    {
        $branchId = activeBranchId();
        $query = DailyProduction::forBranch($branchId)
            ->with(['user', 'items.product'])
            ->orderBy('date', 'desc');

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        $productions = $query->paginate(10)->withQueryString();

        return view('admin.productions.index', compact('productions'));
    }

    public function create()
    {
        $products = \App\Models\Product::orderBy('name')->get();
        return view('admin.productions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array',
        ]);

        $branchId = activeBranchId();

        // Cek jika sudah ada input di tanggal ini
        $existing = DailyProduction::forBranch($branchId)
            ->whereDate('date', $request->date)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data produksi untuk tanggal tersebut sudah ada! Silakan edit data yang sudah ada.');
        }

        DB::transaction(function () use ($request, $branchId) {
            $production = DailyProduction::create([
                'date' => $request->date,
                'branch_id' => $branchId,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->items as $productId => $data) {
                $qty = (int) ($data['quantity_produced'] ?? 0);
                if ($qty > 0) {
                    $production->items()->create([
                        'product_id' => $productId,
                        'quantity_produced' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('admin.productions.index')->with('success', 'Data stok produksi berhasil disimpan.');
    }

    public function show(DailyProduction $production)
    {
        $branchId = activeBranchId();
        if ($production->branch_id !== $branchId) abort(403);

        $production->load(['user', 'items.product', 'branch']);
        return view('admin.productions.show', compact('production'));
    }

    public function edit(DailyProduction $production)
    {
        $products = \App\Models\Product::orderBy('name')->get();
        return view('admin.productions.edit', compact('production', 'products'));
    }

    public function update(Request $request, DailyProduction $production)
    {
        $request->validate([
            'date' => 'required|date',
            'items' => 'required|array',
        ]);

        $branchId = activeBranchId();
        if ($production->branch_id !== $branchId) abort(403);

        // Cek duplikasi tanggal
        $existing = DailyProduction::forBranch($branchId)
            ->whereDate('date', $request->date)
            ->where('id', '!=', $production->id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data produksi untuk tanggal tersebut sudah ada!');
        }

        DB::transaction(function () use ($request, $production) {
            $production->update([
                'date' => $request->date,
            ]);

            $production->items()->delete();

            foreach ($request->items as $productId => $data) {
                $qty = (int) ($data['quantity_produced'] ?? 0);
                if ($qty > 0) {
                    $production->items()->create([
                        'product_id' => $productId,
                        'quantity_produced' => $qty,
                    ]);
                }
            }
        });

        return redirect()->route('admin.productions.index')->with('success', 'Data stok produksi berhasil diperbarui.');
    }

    public function destroy(DailyProduction $production)
    {
        if ($production->branch_id !== activeBranchId()) abort(403);
        $production->delete();
        return redirect()->route('admin.productions.index')->with('success', 'Data stok produksi berhasil dihapus.');
    }
}
