<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\RiderDailySale;
use App\Models\Product;

class RiderSalesReportController extends Controller
{
    public function index(Request $request)
    {
        $riders = User::where('role', 'rider')->get();
        $products = Product::orderBy('id', 'asc')->get();
        $selectedRiderId = $request->rider_id;

        $filterType = $request->filter_type ?? 'monthly';
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $reportData = null;

        if ($selectedRiderId) {
            $rider = User::findOrFail($selectedRiderId);

            // Determine date range (same logic as PayrollController)
            $startDate = null;
            $endDate = null;

            if ($filterType === 'weekly' && $request->week_start) {
                $startDate = \Carbon\Carbon::parse($request->week_start)->startOfDay();
                $endDate = $startDate->copy()->addDays(6)->endOfDay();
            } elseif ($filterType === 'custom' && $request->date_from && $request->date_to) {
                $startDate = \Carbon\Carbon::parse($request->date_from)->startOfDay();
                $endDate = \Carbon\Carbon::parse($request->date_to)->endOfDay();
            } else {
                // Default to Monthly
                $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth()->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();
            }

            // Fetch daily sales with items for the rider in the date range
            $sales = RiderDailySale::with(['items.product'])
                ->where('rider_id', $selectedRiderId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date', 'asc')
                ->get();

            // Aggregate per-product totals
            $productTotals = [];
            foreach ($products as $product) {
                $productTotals[$product->id] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'total_sold' => 0,
                    'total_revenue' => 0,
                ];
            }

            $grandTotalCups = 0;
            $grandTotalRevenue = 0;
            $grandTotalCash = 0;
            $grandTotalQris = 0;

            foreach ($sales as $sale) {
                $grandTotalCash += $sale->cash_amount;
                $grandTotalQris += $sale->qris_amount;
                $grandTotalRevenue += $sale->total_gross_income;

                foreach ($sale->items as $item) {
                    $grandTotalCups += $item->stock_sold;
                    if (isset($productTotals[$item->product_id])) {
                        $productTotals[$item->product_id]['total_sold'] += $item->stock_sold;
                        $productTotals[$item->product_id]['total_revenue'] += $item->stock_sold * $productTotals[$item->product_id]['price'];
                    }
                }
            }

            // Remove products with 0 sales for cleaner display
            $productTotals = array_filter($productTotals, fn($p) => $p['total_sold'] > 0);

            $reportData = [
                'rider' => $rider,
                'filter_type' => $filterType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'month' => $month,
                'year' => $year,
                'sales' => $sales,
                'productTotals' => $productTotals,
                'grandTotalCups' => $grandTotalCups,
                'grandTotalRevenue' => $grandTotalRevenue,
                'grandTotalCash' => $grandTotalCash,
                'grandTotalQris' => $grandTotalQris,
                'totalDays' => $sales->count(),
            ];
        }

        return view('admin.rider_sales_report.index', compact('riders', 'selectedRiderId', 'month', 'year', 'reportData', 'filterType'));
    }
}
