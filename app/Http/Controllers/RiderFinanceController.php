<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\RiderFinance;
use App\Models\User;
use App\Models\RiderDailySale;

class RiderFinanceController extends Controller
{
    public function kasbon(Request $request)
    {
        $riders = User::where('role', 'rider')->forBranch(activeBranchId())->get();
        
        $query = RiderFinance::forBranch(activeBranchId())->with(['rider', 'admin'])->where('type', 'kasbon')->orderBy('date', 'desc');
        
        if ($request->rider_id) {
            $query->where('rider_id', $request->rider_id);
        }
        
        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        $totalKasbon = (clone $query)->sum('amount');
        
        $terbayarQuery = \App\Models\PayrollRecord::forBranch(activeBranchId())
            ->where('status', 'confirmed');
            
        if ($request->rider_id) {
            $terbayarQuery->where('rider_id', $request->rider_id);
        }
        if ($request->start_date) {
            $terbayarQuery->whereDate('confirmed_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $terbayarQuery->whereDate('confirmed_at', '<=', $request->end_date);
        }
        
        $totalTerbayar = $terbayarQuery->sum('kasbon_deducted');
        $totalSisa = $totalKasbon - $totalTerbayar;
        
        $finances = $query->paginate(10)->withQueryString();

        return view('admin.rider_finances.kasbon', compact('finances', 'riders', 'totalKasbon', 'totalTerbayar', 'totalSisa'));
    }

    public function uangMakan(Request $request)
    {
        $riders = User::where('role', 'rider')->forBranch(activeBranchId())->get();
        
        $query = RiderFinance::forBranch(activeBranchId())->with(['rider', 'admin'])->where('type', 'uang_makan')->orderBy('date', 'desc');
        
        if ($request->rider_id) {
            $query->where('rider_id', $request->rider_id);
        }
        
        $month = date('m');
        $year = date('Y');

        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
            $month = \Carbon\Carbon::parse($request->start_date)->format('m');
            $year = \Carbon\Carbon::parse($request->start_date)->format('Y');
        }
        
        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        $totalSudahDiterima = (clone $query)->sum('amount');
        
        // Kalkulasi Estimasi Max dan Sisa Belum Dibayar
        $targetCup = (int) \App\Models\AppSetting::getValue('uang_makan_target_cup', 1040);
        $baseUangMakan = (int) \App\Models\AppSetting::getValue('uang_makan_base_amount', 650000);

        $riderCount = $request->rider_id ? 1 : $riders->count();
        $totalEstimasiMax = $riderCount * $baseUangMakan;

        // Hitung aktual cup untuk periode ini
        $salesQuery = RiderDailySale::forBranch(activeBranchId())->with('items')->whereMonth('date', $month)->whereYear('date', $year);
        if ($request->rider_id) {
            $salesQuery->where('rider_id', $request->rider_id);
        }
        $sales = $salesQuery->get();
        
        // Hitung earned per rider
        $totalEarned = 0;
        $salesByRider = $sales->groupBy('rider_id');
        foreach ($salesByRider as $rId => $riderSales) {
            $cups = $riderSales->sum(function($sale) {
                return $sale->items->sum('stock_sold');
            });
            $earned = $targetCup > 0 ? ($cups / $targetCup) * $baseUangMakan : 0;
            $totalEarned += $earned;
        }

        $sisaBelumDibayar = $totalEarned - $totalSudahDiterima;

        $finances = $query->paginate(10)->withQueryString();

        return view('admin.rider_finances.uang_makan', compact(
            'finances', 'riders', 'totalEstimasiMax', 'totalSudahDiterima', 'sisaBelumDibayar', 'totalEarned'
        ));
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

        $riderFinance = RiderFinance::create([
            'rider_id' => $request->rider_id,
            'admin_id' => auth()->id(),
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'reference_cups' => $request->reference_cups,
            'notes' => $request->notes,
            'branch_id' => activeBranchId()
        ]);

        // Jurnal Otomatis: Kredit (Kas Keluar)
        $typeLabel = $request->type === 'kasbon' ? 'Kasbon Rider' : 'Uang Makan Rider';
        $category = \App\Models\JournalCategory::firstOrCreate(['name' => $typeLabel]);
        $riderName = User::find($request->rider_id)->name ?? 'Unknown';
        
        \App\Models\Journal::create([
            'branch_id' => activeBranchId(),
            'created_by' => auth()->id(),
            'journal_category_id' => $category->id,
            'date' => $request->date,
            'type' => 'credit',
            'amount' => $request->amount,
            'description' => "{$typeLabel}: {$riderName}" . ($request->notes ? " - {$request->notes}" : ''),
        ]);

        $typeLabelMsg = $request->type === 'kasbon' ? 'kasbon' : 'uang makan';
        return redirect()->back()->with('success', "Data {$typeLabelMsg} berhasil disimpan dan dicatat ke Jurnal Umum.");
    }

    public function destroy(RiderFinance $riderFinance)
    {
        $typeLabel = $riderFinance->type === 'kasbon' ? 'Kasbon Rider' : 'Uang Makan Rider';
        $category = \App\Models\JournalCategory::where('name', $typeLabel)->first();
        $riderName = $riderFinance->rider->name ?? 'Unknown';
        $descriptionPrefix = "{$typeLabel}: {$riderName}";

        if ($category) {
            \App\Models\Journal::where('branch_id', $riderFinance->branch_id)
                ->where('journal_category_id', $category->id)
                ->where('type', 'credit')
                ->where('amount', $riderFinance->amount)
                ->where('description', 'like', $descriptionPrefix . '%')
                ->where('date', $riderFinance->date)
                ->delete();
        }

        $riderFinance->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus beserta catatan jurnalnya.');
    }

    public function getCups(Request $request)
    {
        $riderId = $request->rider_id;
        $date = $request->date;
        
        if (!$riderId || !$date) {
            return response()->json(['cups' => 0]);
        }
        
        $sale = RiderDailySale::forBranch(activeBranchId())->with('items')->where('rider_id', $riderId)->where('date', $date)->first();
        
        $totalCups = $sale ? $sale->items->sum('stock_sold') : 0;
        
        return response()->json(['cups' => $totalCups]);
    }
}
