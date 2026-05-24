<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\RiderFinance;
use App\Models\User;
use App\Models\RiderDailySale;

class RiderFinanceController extends Controller
{
    public function index(Request $request)
    {
        $riders = User::where('role', 'rider')->get();
        
        $query = RiderFinance::with(['rider', 'admin'])->orderBy('date', 'desc');
        
        if ($request->rider_id) {
            $query->where('rider_id', $request->rider_id);
        }
        
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        $finances = $query->get();

        return view('admin.rider_finances.index', compact('finances', 'riders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rider_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:kasbon,uang_makan',
            'amount' => 'required|numeric|min:0',
            'reference_cups' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        RiderFinance::create([
            'rider_id' => $request->rider_id,
            'admin_id' => auth()->id(),
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'reference_cups' => $request->reference_cups,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.rider_finances.index')->with('success', 'Data kasbon/uang makan berhasil ditambahkan.');
    }

    public function destroy(RiderFinance $riderFinance)
    {
        $riderFinance->delete();
        return redirect()->route('admin.rider_finances.index')->with('success', 'Data berhasil dihapus.');
    }

    public function getCups(Request $request)
    {
        $riderId = $request->rider_id;
        $date = $request->date;
        
        if (!$riderId || !$date) {
            return response()->json(['cups' => 0]);
        }
        
        $sale = RiderDailySale::with('items')->where('rider_id', $riderId)->where('date', $date)->first();
        
        $totalCups = $sale ? $sale->items->sum('stock_sold') : 0;
        
        return response()->json(['cups' => $totalCups]);
    }
}
