<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiderDailySale;

class RiderMinusController extends Controller
{
    public function index(Request $request)
    {
        $riders = \App\Models\User::where('role', 'rider')->forBranch(activeBranchId())->get();

        $query = RiderDailySale::forBranch(activeBranchId())->with(['rider', 'admin'])
            ->where('minus_amount', '>', 0)
            ->orderBy('date', 'desc');

        if ($request->rider_id) {
            $query->where('rider_id', $request->rider_id);
        }
        
        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        $totalMinus = (clone $query)->sum('minus_amount');
        $minusDibayar = (clone $query)->sum('minus_paid');
        $minusBelumDibayar = $totalMinus - $minusDibayar;
            
        $sales = $query->paginate(10)->withQueryString();
            
        return view('admin.rider_minus.index', compact('sales', 'riders', 'totalMinus', 'minusDibayar', 'minusBelumDibayar'));
    }
}
