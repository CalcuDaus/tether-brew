<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\RiderDailySaleItem;

class ProductionPlanController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.production_plan.index');
    }

    public function data(Request $request)
    {
        try {
            // Default to yesterday
            $date = $request->date ? \Carbon\Carbon::parse($request->date)->format('Y-m-d') : today()->subDay()->format('Y-m-d');
        } catch (\Exception $e) {
            $date = today()->subDay()->format('Y-m-d');
        }
        
        $branchId = activeBranchId();
        
        $products = Product::where('is_active', true)->orderBy('sort_order')->get();
        $planData = [];

        foreach ($products as $product) {
            // "terjual pada riderDailySaleItem yang di konfirmasi oleh admin"
            $soldYesterday = RiderDailySaleItem::whereHas('sale', function($q) use ($branchId, $date) {
                $q->forBranch($branchId)
                  ->whereDate('date', $date)
                  ->whereHas('admin', function($query) {
                      $query->whereIn('role', ['admin', 'owner']);
                  });
            })->where('product_id', $product->id)->sum('stock_sold');

            $planData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'plan_qty' => $soldYesterday,
            ];
        }

        return response()->json([
            'planData' => $planData,
            'date_used' => $date
        ]);
    }
}
