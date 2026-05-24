<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiderDailySale;

class RiderMinusController extends Controller
{
    public function index()
    {
        // Get all sales that have minus_amount > 0
        $sales = RiderDailySale::with(['rider', 'admin'])
            ->where('minus_amount', '>', 0)
            ->orderBy('date', 'desc')
            ->get();
            
        return view('admin.rider_minus.index', compact('sales'));
    }
}
