<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\User;
use App\Models\RiderDailySale;
use App\Models\RiderFinance;
use App\Models\PayrollRecord;
use App\Models\AppSetting;

class PayrollController extends Controller
{
    /**
     * Preview slip gaji (belum committed).
     */
    public function index(Request $request)
    {
        $riders = User::where('role', 'rider')->forBranch(activeBranchId())->get();
        $selectedRiderId = $request->rider_id;
        
        $filterType = $request->filter_type ?? 'weekly';
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        
        // Load settings
        $bonusPerCup = (int) AppSetting::getValue('bonus_per_cup', 2000);
        $targetCup = (int) AppSetting::getValue('uang_makan_target_cup', 1040);
        $baseUangMakan = (int) AppSetting::getValue('uang_makan_base_amount', 650000);
        
        $payrollData = null;

        if ($selectedRiderId) {
            $rider = User::findOrFail($selectedRiderId);
            
            // Determine date range
            $startDate = null;
            $endDate = null;

            if ($filterType === 'weekly' && $request->week_start) {
                $startDate = Carbon::parse($request->week_start)->startOfDay();
                $endDate = $startDate->copy()->addDays(6)->endOfDay();
            } elseif ($filterType === 'custom' && $request->date_from && $request->date_to) {
                $startDate = Carbon::parse($request->date_from)->startOfDay();
                $endDate = Carbon::parse($request->date_to)->endOfDay();
            } else {
                // Default to Monthly
                $filterType = 'monthly';
                $startDate = Carbon::create($year, $month, 1)->startOfMonth()->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();
            }

            // Check if a payroll record already exists for this period (draft or confirmed)
            $existingPayroll = PayrollRecord::where('rider_id', $selectedRiderId)
                ->where('period_start', $startDate->format('Y-m-d'))
                ->where('period_end', $endDate->format('Y-m-d'))
                ->first();

            // 1. Total Sales (within range)
            $sales = RiderDailySale::with('items')
                ->where('rider_id', $selectedRiderId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get();
                
            $totalCups = $sales->sum(function($sale) {
                return $sale->items->sum('stock_sold');
            });
                
            $totalSalesRevenue = $sales->sum('total_gross_income');

            // 2. Outstanding Kasbon = Total kasbon - Total yang sudah pernah dipotong di payroll confirmed
            $totalKasbonAllTime = RiderFinance::where('rider_id', $selectedRiderId)
                ->where('type', 'kasbon')
                ->where('date', '<=', $endDate->format('Y-m-d'))
                ->sum('amount');
            
            $totalKasbonDeducted = PayrollRecord::where('rider_id', $selectedRiderId)
                ->where('status', 'confirmed')
                ->sum('kasbon_deducted');
            
            $outstandingKasbon = max(0, $totalKasbonAllTime - $totalKasbonDeducted);

            // 3. Outstanding Minus = Total minus (all sources) - Total yang sudah dipotong
            $totalMinusAllTime = RiderDailySale::where('rider_id', $selectedRiderId)
                ->where('minus_amount', '>', 0)
                ->where('date', '<=', $endDate->format('Y-m-d'))
                ->sum('minus_amount');

            $totalMinusDeducted = PayrollRecord::where('rider_id', $selectedRiderId)
                ->where('status', 'confirmed')
                ->sum('minus_deducted');
            
            $outstandingMinus = max(0, $totalMinusAllTime - $totalMinusDeducted);

            // Breakdown minus: penjualan vs carry_over (for display)
            $minusPenjualan = RiderDailySale::where('rider_id', $selectedRiderId)
                ->where('minus_amount', '>', 0)
                ->where('minus_source', 'penjualan')
                ->where('date', '<=', $endDate->format('Y-m-d'))
                ->sum('minus_amount');
            
            $minusCarryOver = RiderDailySale::where('rider_id', $selectedRiderId)
                ->where('minus_amount', '>', 0)
                ->where('minus_source', 'carry_over')
                ->where('date', '<=', $endDate->format('Y-m-d'))
                ->sum('minus_amount');

            // 4. Uang Makan (Only for Monthly mode)
            $includeUangMakan = ($filterType === 'monthly');
            $totalUangMakan = 0;
            $achPercentage = 0;
            $earnedUangMakan = 0;
            $sisaUangMakan = 0;

            if ($includeUangMakan) {
                $totalUangMakan = RiderFinance::where('rider_id', $selectedRiderId)
                    ->where('type', 'uang_makan')
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('amount');

                // For monthly, recalculate totalCups for the whole month
                $monthlySales = RiderDailySale::with('items')
                    ->where('rider_id', $selectedRiderId)
                    ->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->get();
                $monthlyCups = $monthlySales->sum(function($sale) {
                    return $sale->items->sum('stock_sold');
                });

                $achPercentage = $targetCup > 0 ? ($monthlyCups / $targetCup) * 100 : 0;
                $earnedUangMakan = $targetCup > 0 ? ($monthlyCups / $targetCup) * $baseUangMakan : 0;
                $sisaUangMakan = $earnedUangMakan - $totalUangMakan;
            }

            // Gross Income = Total Cups * bonus per cup
            $grossIncome = $totalCups * $bonusPerCup;
            
            // Net Income (default: potong semua)
            $netIncome = $grossIncome - $outstandingKasbon - $outstandingMinus;
            if ($includeUangMakan) {
                $netIncome += $sisaUangMakan;
            }

            // Weekly payroll records in this month (for monthly recap)
            $weeklyRecords = [];
            if ($filterType === 'monthly') {
                $weeklyRecords = PayrollRecord::where('rider_id', $selectedRiderId)
                    ->where('type', '!=', 'monthly')
                    ->where(function($q) use ($startDate, $endDate) {
                        $startStr = $startDate->format('Y-m-d');
                        $endStr = $endDate->format('Y-m-d');
                        $q->whereBetween('period_start', [$startStr, $endStr])
                          ->orWhereBetween('period_end', [$startStr, $endStr]);
                    })
                    ->orderBy('period_start')
                    ->get();
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
                'outstandingKasbon' => $outstandingKasbon,
                'outstandingMinus' => $outstandingMinus,
                'minusPenjualan' => $minusPenjualan,
                'minusCarryOver' => $minusCarryOver,
                'totalUangMakan' => $totalUangMakan,
                'targetCup' => $targetCup,
                'achPercentage' => $achPercentage,
                'earnedUangMakan' => $earnedUangMakan,
                'sisaUangMakan' => $sisaUangMakan,
                'grossIncome' => $grossIncome,
                'netIncome' => $netIncome,
                'includeUangMakan' => $includeUangMakan,
                'bonusPerCup' => $bonusPerCup,
                'existingPayroll' => $existingPayroll,
                'weeklyRecords' => $weeklyRecords,
            ];
        }

        return view('admin.payroll.index', compact('riders', 'selectedRiderId', 'month', 'year', 'payrollData', 'filterType'));
    }

    /**
     * Commit/Simpan slip gaji (with custom payment amounts).
     */
    public function store(Request $request)
    {
        $request->validate([
            'rider_id' => 'required|exists:users,id',
            'filter_type' => 'required|in:weekly,monthly,custom',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'kasbon_deducted' => 'required|numeric|min:0',
            'minus_deducted' => 'required|numeric|min:0',
        ]);

        $riderId = $request->rider_id;
        $startDate = Carbon::parse($request->period_start);
        $endDate = Carbon::parse($request->period_end);

        $bonusPerCup = (int) AppSetting::getValue('bonus_per_cup', 2000);
        $targetCup = (int) AppSetting::getValue('uang_makan_target_cup', 1040);
        $baseUangMakan = (int) AppSetting::getValue('uang_makan_base_amount', 650000);

        // Recalculate server-side for security
        $sales = RiderDailySale::with('items')
            ->where('rider_id', $riderId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
        
        $totalCups = $sales->sum(fn($s) => $s->items->sum('stock_sold'));
        $grossIncome = $totalCups * $bonusPerCup;

        // Outstanding calculations
        $totalKasbonAllTime = RiderFinance::where('rider_id', $riderId)
            ->where('type', 'kasbon')
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->sum('amount');
        $totalKasbonDeducted = PayrollRecord::where('rider_id', $riderId)
            ->where('status', 'confirmed')
            ->sum('kasbon_deducted');
        $outstandingKasbon = max(0, $totalKasbonAllTime - $totalKasbonDeducted);

        $totalMinusAllTime = RiderDailySale::where('rider_id', $riderId)
            ->where('minus_amount', '>', 0)
            ->where('date', '<=', $endDate->format('Y-m-d'))
            ->sum('minus_amount');
        $totalMinusDeducted = PayrollRecord::where('rider_id', $riderId)
            ->where('status', 'confirmed')
            ->sum('minus_deducted');
        $outstandingMinus = max(0, $totalMinusAllTime - $totalMinusDeducted);

        // Validate custom payments don't exceed outstanding
        $kasbonDeducted = min($request->kasbon_deducted, $outstandingKasbon);
        $minusDeducted = min($request->minus_deducted, $outstandingMinus);

        // Uang makan for monthly
        $uangMakanAdj = 0;
        if ($request->filter_type === 'monthly') {
            $month = $startDate->month;
            $year = $startDate->year;
            $totalUangMakan = RiderFinance::where('rider_id', $riderId)
                ->where('type', 'uang_makan')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $monthlySales = RiderDailySale::with('items')
                ->where('rider_id', $riderId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();
            $monthlyCups = $monthlySales->sum(fn($s) => $s->items->sum('stock_sold'));
            $earnedUangMakan = $targetCup > 0 ? ($monthlyCups / $targetCup) * $baseUangMakan : 0;
            $uangMakanAdj = $earnedUangMakan - $totalUangMakan;
        }

        $netIncome = $grossIncome - $kasbonDeducted - $minusDeducted;
        if ($request->filter_type === 'monthly') {
            $netIncome += $uangMakanAdj;
        }

        // Check for existing draft for this period
        $payroll = PayrollRecord::updateOrCreate(
            [
                'rider_id' => $riderId,
                'period_start' => $startDate->format('Y-m-d'),
                'period_end' => $endDate->format('Y-m-d'),
            ],
            [
                'admin_id' => auth()->id(),
                'type' => $request->filter_type,
                'total_cups' => $totalCups,
                'gross_income' => $grossIncome,
                'kasbon_outstanding' => $outstandingKasbon,
                'kasbon_deducted' => $kasbonDeducted,
                'minus_outstanding' => $outstandingMinus,
                'minus_deducted' => $minusDeducted,
                'uang_makan_adjustment' => $uangMakanAdj,
                'net_income' => $netIncome,
                'status' => 'draft',
                'notes' => $request->notes,
                'branch_id' => activeBranchId(),
            ]
        );

        return redirect()->route('admin.payroll.show', $payroll->id)
            ->with('success', 'Slip gaji berhasil disimpan.');
    }

    /**
     * Confirm payroll (admin confirms payment has been made).
     */
    public function confirm(PayrollRecord $payrollRecord)
    {
        if ($payrollRecord->status === 'confirmed') {
            return redirect()->back()->with('info', 'Slip gaji ini sudah dikonfirmasi sebelumnya.');
        }

        $payrollRecord->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);

        $branchId = activeBranchId();
        $riderName = $payrollRecord->rider->name;
        $periodeLabel = $payrollRecord->period_start->format('d/m/Y') . ' - ' . $payrollRecord->period_end->format('d/m/Y');

        // 1. GAJI RIDER → Kredit (Kas Keluar)
        $gajiCategory = \App\Models\JournalCategory::firstOrCreate(
            ['name' => 'Gaji Rider']
        );
        \App\Models\Journal::create([
            'date' => now()->format('Y-m-d'),
            'description' => "Gaji rider {$riderName} periode {$periodeLabel}",
            'type' => 'credit',
            'amount' => $payrollRecord->gross_income,
            'journal_category_id' => $gajiCategory->id,
            'created_by' => auth()->id(),
            'branch_id' => $branchId,
        ]);

        // 1.5. PEMBAYARAN KASBON RIDER → Debit (Kas Masuk)
        if ($payrollRecord->kasbon_deducted > 0) {
            $kasbonCategory = \App\Models\JournalCategory::firstOrCreate(
                ['name' => 'Pembayaran Kasbon Rider']
            );

            \App\Models\Journal::create([
                'date' => now()->format('Y-m-d'),
                'description' => "Pembayaran kasbon rider {$riderName} periode {$periodeLabel}",
                'type' => 'debit',
                'amount' => $payrollRecord->kasbon_deducted,
                'journal_category_id' => $kasbonCategory->id,
                'created_by' => auth()->id(),
                'branch_id' => $branchId,
            ]);
        }

        // 2. PEMBAYARAN MINUS RIDER → Debit (Kas Masuk, rider membayar hutang minus via potong gaji)
        if ($payrollRecord->minus_deducted > 0) {
            $minusCategory = \App\Models\JournalCategory::firstOrCreate(
                ['name' => 'Pembayaran Minus Rider']
            );

            \App\Models\Journal::create([
                'date' => now()->format('Y-m-d'),
                'description' => "Pembayaran minus rider {$riderName} periode {$periodeLabel}",
                'type' => 'debit',
                'amount' => $payrollRecord->minus_deducted,
                'journal_category_id' => $minusCategory->id,
                'created_by' => auth()->id(),
                'branch_id' => $branchId,
            ]);
        }

        // 3. UANG MAKAN → Kredit (Kas Keluar, jika ada pembayaran uang makan di periode ini)
        if ($payrollRecord->uang_makan_adjustment != 0) {
            $uangMakanCategory = \App\Models\JournalCategory::firstOrCreate(
                ['name' => 'Uang Makan Rider']
            );

            if ($payrollRecord->uang_makan_adjustment > 0) {
                // Sisa uang makan yang harus dibayar ke rider → Kredit
                \App\Models\Journal::create([
                    'date' => now()->format('Y-m-d'),
                    'description' => "Uang makan rider {$riderName} periode {$periodeLabel}",
                    'type' => 'credit',
                    'amount' => $payrollRecord->uang_makan_adjustment,
                    'journal_category_id' => $uangMakanCategory->id,
                    'created_by' => auth()->id(),
                    'branch_id' => $branchId,
                ]);
            } else {
                // Kelebihan uang makan (sudah dibayar lebih) → Debit
                \App\Models\Journal::create([
                    'date' => now()->format('Y-m-d'),
                    'description' => "Kelebihan uang makan rider {$riderName} periode {$periodeLabel}",
                    'type' => 'debit',
                    'amount' => abs($payrollRecord->uang_makan_adjustment),
                    'journal_category_id' => $uangMakanCategory->id,
                    'created_by' => auth()->id(),
                    'branch_id' => $branchId,
                ]);
            }
        }

        // Distribusi pembayaran minus ke record RiderDailySale
        $deducted = $payrollRecord->minus_deducted;
        if ($deducted > 0) {
            $unpaidSales = RiderDailySale::where('rider_id', $payrollRecord->rider_id)
                ->whereIn('minus_status', ['unpaid', 'partial'])
                ->orderBy('date', 'asc')
                ->get();
                
            foreach ($unpaidSales as $sale) {
                if ($deducted <= 0) break;
                
                $remainingMinus = $sale->minus_amount - $sale->minus_paid;
                if ($remainingMinus <= $deducted) {
                    $sale->minus_paid += $remainingMinus;
                    $sale->minus_status = 'paid';
                    $deducted -= $remainingMinus;
                } else {
                    $sale->minus_paid += $deducted;
                    $sale->minus_status = 'partial';
                    $deducted = 0;
                }
                $sale->save();
            }
        }

        // Similarly for sisa kasbon — it stays outstanding automatically
        // (next payroll will recalculate outstanding from total - total_deducted)

        return redirect()->route('admin.payroll.show', $payrollRecord->id)
            ->with('success', 'Slip gaji berhasil dikonfirmasi! Pembayaran telah dicatat ke jurnal umum.');
    }

    /**
     * Show/reprint a committed payroll record.
     */
    public function show(PayrollRecord $payrollRecord)
    {
        $payrollRecord->load(['rider', 'admin', 'confirmedByAdmin']);

        $bonusPerCup = (int) AppSetting::getValue('bonus_per_cup', 2000);
        $targetCup = (int) AppSetting::getValue('uang_makan_target_cup', 1040);
        $baseUangMakan = (int) AppSetting::getValue('uang_makan_base_amount', 650000);

        // Weekly records recap for monthly
        $weeklyRecords = [];
        if ($payrollRecord->type === 'monthly') {
            $weeklyRecords = PayrollRecord::where('rider_id', $payrollRecord->rider_id)
                ->where('type', '!=', 'monthly')
                ->where(function($q) use ($payrollRecord) {
                    $startStr = $payrollRecord->period_start->format('Y-m-d');
                    $endStr = $payrollRecord->period_end->format('Y-m-d');
                    $q->whereBetween('period_start', [$startStr, $endStr])
                      ->orWhereBetween('period_end', [$startStr, $endStr]);
                })
                ->orderBy('period_start')
                ->get();
        }

        return view('admin.payroll.show', compact('payrollRecord', 'bonusPerCup', 'targetCup', 'baseUangMakan', 'weeklyRecords'));
    }

    /**
     * List all payroll history.
     */
    public function history(Request $request)
    {
        $riders = User::where('role', 'rider')->forBranch(activeBranchId())->get();

        $query = PayrollRecord::forBranch(activeBranchId())->with(['rider', 'admin'])->orderBy('created_at', 'desc');

        if ($request->rider_id) {
            $query->where('rider_id', $request->rider_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->month && $request->year) {
            $query->whereMonth('period_start', $request->month)
                  ->whereYear('period_start', $request->year);
        }

        $records = $query->paginate(15)->withQueryString();

        return view('admin.payroll.history', compact('records', 'riders'));
    }
}
