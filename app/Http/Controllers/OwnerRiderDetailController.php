<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RiderDailySale;
use App\Models\RiderDailySaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OwnerRiderDetailController extends Controller
{
    public function show(Request $request, User $rider)
    {
        abort_if($rider->role !== 'rider', 404);

        $days = $request->input('days', 30);
        $startDate = now()->subDays($days)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        // Daily sales data
        $sales = RiderDailySale::with(['items.product'])
            ->where('rider_id', $rider->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        // Aggregates
        $totalCups = 0;
        $totalRevenue = 0;
        $totalCash = 0;
        $totalQris = 0;
        $totalDays = $sales->count();

        $dailyChart = [];
        $productTotals = [];

        foreach ($sales as $sale) {
            $totalCash += $sale->cash_amount;
            $totalQris += $sale->qris_amount;
            $totalRevenue += $sale->total_gross_income;

            $dayCups = 0;
            foreach ($sale->items as $item) {
                $dayCups += $item->stock_sold;
                $totalCups += $item->stock_sold;

                $pid = $item->product_id;
                if (!isset($productTotals[$pid])) {
                    $productTotals[$pid] = [
                        'name' => $item->product->name ?? 'Unknown',
                        'price' => $item->product->price ?? 0,
                        'total_sold' => 0,
                        'total_revenue' => 0,
                    ];
                }
                $productTotals[$pid]['total_sold'] += $item->stock_sold;
                $productTotals[$pid]['total_revenue'] += $item->stock_sold * $productTotals[$pid]['price'];
            }

            $dailyChart[] = [
                'date' => $sale->date->format('Y-m-d'),
                'cups' => $dayCups,
                'revenue' => (int) $sale->total_gross_income,
            ];
        }

        // Remove zero-sale products
        $productTotals = array_filter($productTotals, fn($p) => $p['total_sold'] > 0);
        usort($productTotals, fn($a, $b) => $b['total_sold'] - $a['total_sold']);

        return view('dashboard.owner_rider_detail', compact(
            'rider', 'days', 'startDate', 'endDate',
            'sales', 'dailyChart',
            'totalCups', 'totalRevenue', 'totalCash', 'totalQris', 'totalDays',
            'productTotals'
        ));
    }
}
