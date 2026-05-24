<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\RiderDailySale;
use App\Models\RiderFinance;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $riders = User::where('role', 'rider')->get();
        $selectedRiderId = $request->rider_id;
        
        $filterType = $request->filter_type ?? 'monthly';
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        $payrollData = null;

        if ($selectedRiderId) {
            $rider = User::findOrFail($selectedRiderId);
            
            // Determine date range
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

            // 1. Total Sales (within range)
            $sales = RiderDailySale::with('items')
                ->where('rider_id', $selectedRiderId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();
                
            $totalCups = $sales->sum(function($sale) {
                return $sale->items->sum('stock_sold');
            });
                
            $totalSalesRevenue = $sales->sum('total_gross_income');

            // 2. Total Kasbon (Up to endDate - as requested: "dengan tanggal sebelumnya juga")
            $totalKasbon = RiderFinance::where('rider_id', $selectedRiderId)
                ->where('type', 'kasbon')
                ->where('date', '<=', $endDate->format('Y-m-d'))
                ->sum('amount');

            // 3. Total Minus (Up to endDate)
            // We need to fetch all sales before endDate to get the accumulated minus
            $allSalesBefore = RiderDailySale::where('rider_id', $selectedRiderId)
                ->where('date', '<=', $endDate->format('Y-m-d'))
                ->get();
            $totalMinus = $allSalesBefore->sum('minus_amount');

            // 4. Uang Makan (Only for Monthly mode)
            $includeUangMakan = ($filterType === 'monthly');
            $totalUangMakan = 0;
            $targetCup = 1040;
            $baseUangMakan = 650000;
            $achPercentage = 0;
            $earnedUangMakan = 0;
            $sisaUangMakan = 0;

            if ($includeUangMakan) {
                $totalUangMakan = RiderFinance::where('rider_id', $selectedRiderId)
                    ->where('type', 'uang_makan')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('amount');

                $achPercentage = $targetCup > 0 ? ($totalCups / $targetCup) * 100 : 0;
                $earnedUangMakan = $targetCup > 0 ? ($totalCups / $targetCup) * $baseUangMakan : 0;
                $sisaUangMakan = $earnedUangMakan - $totalUangMakan;
            }

            // Gross Income = Total Cups * 2000
            $grossIncome = $totalCups * 2000;
            
            // Net Income
            $netIncome = $grossIncome - $totalKasbon - $totalMinus;
            if ($includeUangMakan) {
                $netIncome += $sisaUangMakan;
            }

            $payrollData = [
                'rider' => $rider,
                'filter_type' => $filterType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'month' => $month,
                'year' => $year,
                'totalCups' => $totalCups,
                'totalSalesRevenue' => $totalSalesRevenue,
                'totalKasbon' => $totalKasbon,
                'totalMinus' => $totalMinus,
                'totalUangMakan' => $totalUangMakan,
                'targetCup' => $targetCup,
                'achPercentage' => $achPercentage,
                'earnedUangMakan' => $earnedUangMakan,
                'sisaUangMakan' => $sisaUangMakan,
                'grossIncome' => $grossIncome,
                'netIncome' => $netIncome,
                'includeUangMakan' => $includeUangMakan,
            ];
        }

        return view('admin.payroll.index', compact('riders', 'selectedRiderId', 'month', 'year', 'payrollData', 'filterType'));
    }
}
