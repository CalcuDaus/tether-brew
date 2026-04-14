<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isOwner() || $user->isAdmin()) {
            return $this->ownerDashboard();
        }

        return $this->riderDashboard($user);
    }

    private function ownerDashboard()
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

        return view('dashboard.owner', compact(
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

