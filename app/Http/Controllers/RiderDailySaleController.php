<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\RiderDailySale;

class RiderDailySaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = RiderDailySale::with(['rider', 'admin'])->orderBy('date', 'desc')->get();
        return view('admin.rider_sales.index', compact('sales'));
    }

    public function create()
    {
        $riders = User::where('role', 'rider')->get();
        $products = \App\Models\Product::orderBy('id', 'asc')->get();
        return view('admin.rider_sales.create', compact('riders', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'rider_id' => 'required|exists:users,id',
            'admin_pemeriksa' => 'nullable|string',
            'items' => 'required|array',
            'cash_amount' => 'nullable|numeric',
            'actual_setor' => 'required|numeric',
            'qris_amount' => 'nullable|numeric',
            'total_gross_income' => 'nullable|numeric',
        ]);

        $minus = max(0, ($request->cash_amount ?? 0) - $request->actual_setor);

        $sale = RiderDailySale::updateOrCreate(
            [
                'rider_id' => $request->rider_id,
                'date' => $request->date,
            ],
            [
                'cash_amount' => $request->cash_amount ?? 0,
                'actual_setor' => $request->actual_setor,
                'minus_amount' => $minus,
                'qris_amount' => $request->qris_amount ?? 0,
                'total_setoran' => ($request->cash_amount ?? 0) + ($request->qris_amount ?? 0),
                'total_gross_income' => $request->total_gross_income ?? 0,
                'admin_pemeriksa' => $request->admin_pemeriksa,
                'admin_id' => auth()->id()
            ]
        );

        foreach ($request->items as $productId => $itemData) {
            \App\Models\RiderDailySaleItem::updateOrCreate(
                [
                    'rider_daily_sale_id' => $sale->id,
                    'product_id' => $productId,
                ],
                [
                    'stock_out' => $itemData['stock_out'] ?? 0,
                    'stock_added' => $itemData['stock_added'] ?? 0,
                    'stock_return' => $itemData['stock_return'] ?? 0,
                    'stock_sold' => $itemData['stock_sold'] ?? 0,
                ]
            );
        }

        return redirect()->route('admin.rider_sales.index')->with('success', 'Data penjualan harian berhasil disimpan.');
    }

    public function edit(RiderDailySale $riderSale)
    {
        $riders = User::where('role', 'rider')->get();
        $products = \App\Models\Product::orderBy('id', 'asc')->get();
        $riderSale->load('items');
        return view('admin.rider_sales.edit', compact('riderSale', 'riders', 'products'));
    }

    public function update(Request $request, RiderDailySale $riderSale)
    {
        $request->validate([
            'date' => 'required|date',
            'rider_id' => 'required|exists:users,id',
            'admin_pemeriksa' => 'nullable|string',
            'items' => 'required|array',
            'cash_amount' => 'nullable|numeric',
            'actual_setor' => 'required|numeric',
            'qris_amount' => 'nullable|numeric',
            'total_gross_income' => 'nullable|numeric',
        ]);

        $minus = max(0, ($request->cash_amount ?? 0) - $request->actual_setor);

        $riderSale->update([
            'rider_id' => $request->rider_id,
            'date' => $request->date,
            'cash_amount' => $request->cash_amount ?? 0,
            'actual_setor' => $request->actual_setor,
            'minus_amount' => $minus,
            'qris_amount' => $request->qris_amount ?? 0,
            'total_setoran' => ($request->cash_amount ?? 0) + ($request->qris_amount ?? 0),
            'total_gross_income' => $request->total_gross_income ?? 0,
            'admin_pemeriksa' => $request->admin_pemeriksa,
            'admin_id' => auth()->id()
        ]);

        foreach ($request->items as $productId => $itemData) {
            \App\Models\RiderDailySaleItem::updateOrCreate(
                [
                    'rider_daily_sale_id' => $riderSale->id,
                    'product_id' => $productId,
                ],
                [
                    'stock_out' => $itemData['stock_out'] ?? 0,
                    'stock_added' => $itemData['stock_added'] ?? 0,
                    'stock_return' => $itemData['stock_return'] ?? 0,
                    'stock_sold' => $itemData['stock_sold'] ?? 0,
                ]
            );
        }

        return redirect()->route('admin.rider_sales.index')->with('success', 'Data penjualan harian berhasil diupdate.');
    }
}
