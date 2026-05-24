<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\RiderDailySale;
use App\Models\RiderDailySaleItem;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isOwner()) {
            return $this->ownerDashboard();
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->riderDashboard($user);
    }

    private function ownerDashboard()
    {
        $riders = User::where('role', 'rider')->get();

        // --- Stat Cards ---
        $totalRiders = $riders->count();

        // Month revenue from RiderDailySale
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthSales = RiderDailySale::whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->get();
        $monthRevenue = $monthSales->sum('total_gross_income');

        $monthCups = RiderDailySaleItem::whereHas('sale', function ($q) use ($monthStart, $monthEnd) {
            $q->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')]);
        })->sum('stock_sold');

        $todaySales = RiderDailySale::whereDate('date', today())->get();
        $todayRevenue = $todaySales->sum('total_gross_income');

        // --- Chart 1: Rider Performance Bar (30 days) ---
        $thirtyDaysAgo = now()->subDays(30)->format('Y-m-d');
        $today = now()->format('Y-m-d');

        $riderPerformance = [];
        foreach ($riders as $rider) {
            $sales = RiderDailySale::where('rider_id', $rider->id)
                ->whereBetween('date', [$thirtyDaysAgo, $today])
                ->get();

            $totalCups = RiderDailySaleItem::whereIn('rider_daily_sale_id', $sales->pluck('id'))
                ->sum('stock_sold');

            $totalRevenue = $sales->sum('total_gross_income');

            $riderPerformance[] = [
                'id' => $rider->id,
                'name' => $rider->name,
                'cups' => (int) $totalCups,
                'revenue' => (int) $totalRevenue,
            ];
        }

        // Sort by cups desc
        usort($riderPerformance, fn($a, $b) => $b['cups'] - $a['cups']);

        // --- Chart 2: Daily Revenue Trend (14 days) ---
        $dailyRevenue = RiderDailySale::select(
                DB::raw('date'),
                DB::raw('SUM(total_gross_income) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->where('date', '>=', now()->subDays(14)->format('Y-m-d'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // --- Chart 3: Payment Method Distribution (this month) ---
        $totalCash = $monthSales->sum('cash_amount');
        $totalQris = $monthSales->sum('qris_amount');

        // --- Chart 4: Top Products (30 days) ---
        $topProducts = RiderDailySaleItem::select('product_id', DB::raw('SUM(stock_sold) as total_sold'))
            ->whereHas('sale', function ($q) use ($thirtyDaysAgo, $today) {
                $q->whereBetween('date', [$thirtyDaysAgo, $today]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->limit(8)
            ->get();

        // --- Journal Summary ---
        $journalDebit = Journal::where('type', 'debit')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $journalCredit = Journal::where('type', 'credit')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $journalBalance = $journalDebit - $journalCredit;

        return view('dashboard.owner', compact(
            'riders', 'totalRiders',
            'monthRevenue', 'monthCups', 'todayRevenue',
            'riderPerformance', 'dailyRevenue',
            'totalCash', 'totalQris',
            'topProducts',
            'journalDebit', 'journalCredit', 'journalBalance'
        ));
    }

    private function adminDashboard()
    {
        $totalCarts = Cart::count();
        $activeCarts = Cart::where('status', 'active')->count();
        $totalProducts = Product::count();

        $todayRevenue = Transaction::whereDate('created_at', today())->sum('total_price');
        $weekRevenue = Transaction::where('created_at', '>=', now()->startOfWeek())->sum('total_price');
        $monthRevenue = Transaction::where('created_at', '>=', now()->startOfMonth())->sum('total_price');

        $todayTransactions = Transaction::whereDate('created_at', today())->count();

        // Top selling products
        $topProducts = TransactionItem::select('product_id', DB::raw('SUM(qty) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->with('product')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Daily revenue chart (last 7 days)
        $dailyRevenue = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::with(['cart', 'user', 'items.product'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalCarts', 'activeCarts', 'totalProducts',
            'todayRevenue', 'weekRevenue', 'monthRevenue',
            'todayTransactions', 'topProducts', 'dailyRevenue',
            'recentTransactions'
        ));
    }

    private function riderDashboard($user)
    {
        $cart = Cart::where('user_id', $user->id)->with(['location', 'inventories.product'])->first();

        $todayTransactions = [];
        $todayRevenue = 0;
        $todayCount = 0;

        if ($cart) {
            $todayTransactions = Transaction::where('cart_id', $cart->id)
                ->whereDate('created_at', today())
                ->with('items.product')
                ->latest()
                ->get();
            $todayRevenue = $todayTransactions->sum('total_price');
            $todayCount = $todayTransactions->count();
        }

        return view('dashboard.rider', compact('cart', 'todayTransactions', 'todayRevenue', 'todayCount'));
    }
}
