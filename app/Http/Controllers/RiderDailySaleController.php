<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\RiderDailySale;

class RiderDailySaleController extends Controller
{
    public function index(Request $request)
    {
        $branchId = activeBranchId();
        $query = RiderDailySale::forBranch($branchId)
            ->with(['rider', 'admin'])
            ->withSum('items as total_cups', 'stock_sold')
            ->orderBy('date', 'desc');

        $filterDate = $request->get('date');
        if ($filterDate) {
            $query->whereDate('date', $filterDate);
        }

        $search = $request->get('search');
        if ($search) {
            $query->whereHas('rider', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $sales = $query->paginate(15)->withQueryString();

        $summary = null;
        $confirmation = null;

        if ($filterDate) {
            $summaryQuery = RiderDailySale::forBranch($branchId)->whereDate('date', $filterDate);
            if ($search) {
                $summaryQuery->whereHas('rider', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }
            
            $totalCups = \App\Models\RiderDailySaleItem::whereHas('sale', function($q) use ($branchId, $filterDate) {
                $q->forBranch($branchId)->whereDate('date', $filterDate);
            })->sum('stock_sold');

            $summary = [
                'total_cash' => $summaryQuery->sum('cash_amount'),
                'total_qris' => $summaryQuery->sum('qris_amount'),
                'total_minus' => $summaryQuery->sum('minus_amount'),
                'total_omset' => $summaryQuery->sum('total_gross_income'),
                'total_cups' => $totalCups,
                'rider_count' => $summaryQuery->distinct('rider_id')->count('rider_id'),
            ];

            $confirmation = \App\Models\RiderSalesJournalConfirmation::forBranch($branchId)
                ->whereDate('date', $filterDate)
                ->with('confirmedByAdmin')
                ->first();
                
            $unverifiedCount = RiderDailySale::forBranch($branchId)
                ->whereDate('date', $filterDate)
                ->where(function($q) {
                    $q->whereNull('admin_id')
                      ->orWhereHas('admin', function($query) {
                          $query->whereNotIn('role', ['admin', 'owner']);
                      });
                })->count();
        } else {
            $unverifiedCount = 0;
        }

        return view('admin.rider_sales.index', compact('sales', 'filterDate', 'search', 'summary', 'confirmation', 'unverifiedCount'));
    }

    public function availableStock(Request $request)
    {
        try {
            $date = $request->date ? \Carbon\Carbon::parse($request->date)->format('Y-m-d') : today()->format('Y-m-d');
        } catch (\Exception $e) {
            $date = today()->format('Y-m-d');
        }
        $branchId = activeBranchId();
        
        $products = \App\Models\Product::orderBy('id', 'asc')->get();
        $stockData = [];

        // Get existing sale for this rider today to prefill
        $riderSale = null;
        if ($request->rider_id) {
            $riderSale = \App\Models\RiderDailySale::forBranch($branchId)
                ->whereDate('date', $date)
                ->where('rider_id', $request->rider_id)
                ->with('items')
                ->first();
        }

        foreach ($products as $product) {
            $produced = \App\Models\DailyProductionItem::whereHas('dailyProduction', function($q) use ($branchId, $date) {
                $q->forBranch($branchId)->whereDate('date', '<=', $date);
            })->where('product_id', $product->id)->sum('quantity_produced');

            $spoiledQty = \App\Models\SpoiledProductItem::whereHas('spoiledProduct', function($q) use ($branchId, $date) {
                $q->forBranch($branchId)->whereDate('date', '<=', $date);
            })->where('product_id', $product->id)->sum('quantity');

            $usedQuery = \App\Models\RiderDailySaleItem::whereHas('sale', function($q) use ($branchId, $date, $request) {
                $q->forBranch($branchId)->whereDate('date', '<=', $date)
                  ->when($request->rider_id, function($query) use ($request, $date) {
                      return $query->where(function($q2) use ($request, $date) {
                          $q2->whereDate('date', '<', $date)
                             ->orWhere(function($q3) use ($request, $date) {
                                 $q3->whereDate('date', $date)
                                    ->where('rider_id', '!=', $request->rider_id);
                             });
                      });
                  });
            })->where('product_id', $product->id);

            $out = clone $usedQuery;
            $add = clone $usedQuery;
            $ret = clone $usedQuery;

            $used = $out->sum('stock_out') + $add->sum('stock_added') - $ret->sum('stock_return');

            $available = max(0, $produced - $used - $spoiledQty);

            $riderItemData = null;
            if ($riderSale) {
                $saleItem = $riderSale->items->where('product_id', $product->id)->first();
                if ($saleItem) {
                    $riderItemData = [
                        'stock_out' => $saleItem->stock_out,
                        'stock_added' => $saleItem->stock_added,
                        'stock_return' => $saleItem->stock_return,
                        'stock_sold' => $saleItem->stock_sold,
                    ];
                }
            }

            $stockData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'available' => $available,
                'produced' => $produced,
                'used' => $used,
                'spoiled' => $spoiledQty,
                'rider_item' => $riderItemData,
            ];
        }

        return response()->json([
            'stockData' => $stockData,
            'riderSale' => $riderSale ? [
                'cash_amount' => $riderSale->cash_amount,
                'qris_amount' => $riderSale->qris_amount,
                'actual_setor' => $riderSale->actual_setor,
                'minus_amount' => $riderSale->minus_amount,
                'total_gross_income' => $riderSale->total_gross_income,
                'admin_pemeriksa' => $riderSale->admin_pemeriksa,
            ] : null
        ]);
    }

    public function create()
    {
        $riders = User::where('role', 'rider')->forBranch(activeBranchId())->get();
        $products = \App\Models\Product::orderBy('id', 'asc')->get();
        return view('admin.rider_sales.create', compact('riders', 'products'));
    }

    public function confirmJournal(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = $request->date;
        $branchId = activeBranchId();

        $existing = \App\Models\RiderSalesJournalConfirmation::forBranch($branchId)
            ->whereDate('date', $date)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Penjualan tanggal ini sudah dikonfirmasi.');
        }

        $sales = RiderDailySale::forBranch($branchId)->whereDate('date', $date)->with('admin')->get();
        if ($sales->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data penjualan untuk tanggal ini.');
        }

        $unverifiedCount = $sales->filter(function($sale) {
            return !($sale->admin && in_array($sale->admin->role, ['admin', 'owner']));
        })->count();

        if ($unverifiedCount > 0) {
            return redirect()->back()->with('error', "Terdapat {$unverifiedCount} data penjualan yang belum Anda periksa. Harap periksa semua data sebelum mengonfirmasi ke Jurnal Umum.");
        }

        $totalCash = $sales->sum('cash_amount');
        $totalQris = $sales->sum('qris_amount');
        $totalMinus = $sales->sum('minus_amount');
        $totalOmset = $sales->sum('total_gross_income');
        $totalActualSetor = $sales->sum('actual_setor');
        $riderCount = $sales->unique('rider_id')->count();

        \Illuminate\Support\Facades\DB::transaction(function () use ($date, $branchId, $totalCash, $totalQris, $totalMinus, $totalOmset, $totalActualSetor, $riderCount) {
            \App\Models\RiderSalesJournalConfirmation::create([
                'date' => $date,
                'branch_id' => $branchId,
                'total_cash' => $totalCash,
                'total_qris' => $totalQris,
                'total_minus' => $totalMinus,
                'total_omset' => $totalOmset,
                'rider_count' => $riderCount,
                'confirmed_by' => auth()->id(),
            ]);

            $category = \App\Models\JournalCategory::firstOrCreate(
                ['name' => 'Penjualan Cabang']
            );

            if ($totalActualSetor > 0) {
                \App\Models\Journal::create([
                    'branch_id' => $branchId,
                    'journal_category_id' => $category->id,
                    'date' => $date,
                    'type' => 'debit',
                    'amount' => $totalActualSetor,
                    'description' => "Penjualan Rider (CASH) - " . \Carbon\Carbon::parse($date)->format('d/m/Y'),
                    'created_by' => auth()->id()
                ]);
            }
            
            if ($totalQris > 0) {
                \App\Models\Journal::create([
                    'branch_id' => $branchId,
                    'journal_category_id' => $category->id,
                    'date' => $date,
                    'type' => 'debit',
                    'amount' => $totalQris,
                    'description' => "Penjualan Rider (QRIS) - " . \Carbon\Carbon::parse($date)->format('d/m/Y'),
                    'created_by' => auth()->id()
                ]);
            }
        });

        return redirect()->back()->with('success', 'Berhasil mengonfirmasi penjualan ke Jurnal Umum.');
    }

    public function rollbackJournal(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = $request->date;
        $branchId = activeBranchId();

        $confirmation = \App\Models\RiderSalesJournalConfirmation::forBranch($branchId)
            ->whereDate('date', $date)
            ->first();

        if (!$confirmation) {
            return redirect()->back()->with('error', 'Konfirmasi jurnal tidak ditemukan.');
        }

        // Limit to 2 days
        if (now()->diffInDays($confirmation->created_at) > 2) {
            return redirect()->back()->with('error', 'Data yang sudah dikonfirmasi lebih dari 2 hari tidak dapat dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($date, $branchId, $confirmation) {
            $confirmation->delete();

            $descCash = "Penjualan Rider (CASH) - " . \Carbon\Carbon::parse($date)->format('d/m/Y');
            $descQris = "Penjualan Rider (QRIS) - " . \Carbon\Carbon::parse($date)->format('d/m/Y');

            \App\Models\Journal::forBranch($branchId)
                ->whereDate('date', $date)
                ->whereIn('description', [$descCash, $descQris])
                ->delete();
        });

        return redirect()->back()->with('success', 'Konfirmasi Jurnal Umum berhasil dibatalkan. Jurnal telah dihapus.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'rider_id' => 'required|exists:users,id',
            'admin_pemeriksa' => 'nullable|string',
            'items' => 'required|array',
            'cash_amount' => 'nullable|numeric',
            'actual_setor' => auth()->user()->isBar() ? 'nullable|numeric' : 'required|numeric',
            'qris_amount' => 'nullable|numeric',
            'total_gross_income' => 'nullable|numeric',
        ]);

        $actualSetor = $request->actual_setor ?? ($request->cash_amount ?? 0);
        $minus = max(0, ($request->cash_amount ?? 0) - $actualSetor);

        $sale = RiderDailySale::updateOrCreate(
            [
                'rider_id' => $request->rider_id,
                'date' => $request->date,
            ],
            [
                'branch_id' => activeBranchId(),
                'cash_amount' => $request->cash_amount ?? 0,
                'actual_setor' => $actualSetor,
                'minus_amount' => $minus,
                'qris_amount' => $request->qris_amount ?? 0,
                'total_setoran' => ($request->cash_amount ?? 0) + ($request->qris_amount ?? 0),
                'total_gross_income' => $request->total_gross_income ?? 0,
                'admin_pemeriksa' => $request->admin_pemeriksa,
                'admin_id' => auth()->id()
            ]
        );

        foreach ($request->items as $productId => $itemData) {
            \App\Models\RiderDailySaleItem::updateOrCreate(
                [
                    'rider_daily_sale_id' => $sale->id,
                    'product_id' => $productId,
                ],
                [
                    'branch_id' => activeBranchId(),
                    'stock_out' => $itemData['stock_out'] ?? 0,
                    'stock_added' => $itemData['stock_added'] ?? 0,
                    'stock_return' => $itemData['stock_return'] ?? 0,
                    'stock_sold' => $itemData['stock_sold'] ?? 0,
                ]
            );
        }

        return redirect()->route('admin.rider_sales.index')->with('success', 'Data penjualan harian berhasil disimpan.');
    }

    public function edit(RiderDailySale $riderSale)
    {
        $riders = User::where('role', 'rider')->forBranch(activeBranchId())->get();
        $products = \App\Models\Product::orderBy('id', 'asc')->get();
        $riderSale->load('items');
        return view('admin.rider_sales.edit', compact('riderSale', 'riders', 'products'));
    }

    public function update(Request $request, RiderDailySale $riderSale)
    {
        $request->validate([
            'date' => 'required|date',
            'rider_id' => 'required|exists:users,id',
            'admin_pemeriksa' => 'nullable|string',
            'items' => 'required|array',
            'cash_amount' => 'nullable|numeric',
            'actual_setor' => auth()->user()->isBar() ? 'nullable|numeric' : 'required|numeric',
            'qris_amount' => 'nullable|numeric',
            'total_gross_income' => 'nullable|numeric',
        ]);

        $actualSetor = $request->actual_setor ?? ($request->cash_amount ?? 0);
        $minus = max(0, ($request->cash_amount ?? 0) - $actualSetor);

        $riderSale->update([
            'rider_id' => $request->rider_id,
            'date' => $request->date,
            'cash_amount' => $request->cash_amount ?? 0,
            'actual_setor' => $actualSetor,
            'minus_amount' => $minus,
            'qris_amount' => $request->qris_amount ?? 0,
            'total_setoran' => ($request->cash_amount ?? 0) + ($request->qris_amount ?? 0),
            'total_gross_income' => $request->total_gross_income ?? 0,
            'admin_pemeriksa' => $request->admin_pemeriksa,
            'admin_id' => auth()->id()
        ]);

        foreach ($request->items as $productId => $itemData) {
            \App\Models\RiderDailySaleItem::updateOrCreate(
                [
                    'rider_daily_sale_id' => $riderSale->id,
                    'product_id' => $productId,
                ],
                [
                    'branch_id' => activeBranchId(),
                    'stock_out' => $itemData['stock_out'] ?? 0,
                    'stock_added' => $itemData['stock_added'] ?? 0,
                    'stock_return' => $itemData['stock_return'] ?? 0,
                    'stock_sold' => $itemData['stock_sold'] ?? 0,
                ]
            );
        }

        return redirect()->route('admin.rider_sales.index')->with('success', 'Data penjualan harian berhasil diupdate.');
    }
}
