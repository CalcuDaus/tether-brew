<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Branch;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\AppSetting;
use App\Models\JournalCategory;
use App\Models\RiderDailySale;
use App\Models\RiderDailySaleItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Journal;
use App\Models\RiderFinance;
use Carbon\Carbon;

class DummyJuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch1 = Branch::find(1);
        $branch2 = Branch::find(2);

        if (!$branch2) {
            $this->command->error("Branch 2 (SM Raja) not found. Please ensure it exists.");
            return;
        }

        $this->command->info("Starting data seed for Branch 1 and Branch 2...");

        // ==========================================
        // 1. SETUP BRANCH 2 INFRASTRUCTURE
        // ==========================================
        $this->command->info("Setting up Branch 2 infrastructure...");

        // App Settings for Branch 2
        AppSetting::firstOrCreate(['branch_id' => 2, 'key' => 'uang_makan_target_cup'], ['value' => '1040', 'label' => 'Target Cup Uang Makan (per bulan)']);
        AppSetting::firstOrCreate(['branch_id' => 2, 'key' => 'uang_makan_base_amount'], ['value' => '650000', 'label' => 'Base Uang Makan (Rp)']);
        AppSetting::firstOrCreate(['branch_id' => 2, 'key' => 'bonus_per_cup'], ['value' => '2000', 'label' => 'Bonus per Cup (Rp)']);

        // Journal Categories for Branch 2
        $categories = [
            'BAHAN BAKU',
            'NON BAHAN BAKU',
            'LAIN LAIN',
            'PENJUALAN CASH',
            'PENJUALAN QRIS',
            'PENJUALAN ONLINE',
            'PERBAIKAN',
            'PEMBAYARAN GAJI RIDER',
            'PEMBAYARAN GAJI BAR',
        ];
        
        $b2CategoryIds = [];
        foreach ($categories as $catName) {
            $cat = JournalCategory::firstOrCreate(['branch_id' => 2, 'name' => $catName]);
            $b2CategoryIds[$catName] = $cat->id;
        }
        
        // Get Branch 1 category IDs for later use
        $b1CategoryIds = [];
        foreach (JournalCategory::where('branch_id', 1)->get() as $cat) {
            $b1CategoryIds[$cat->name] = $cat->id;
        }

        // Products for Branch 2 (Mirror Branch 1)
        $b1Products = Product::where('branch_id', 1)->get();
        if (Product::where('branch_id', 2)->count() == 0) {
            foreach ($b1Products as $product) {
                Product::create([
                    'branch_id' => 2,
                    'name' => $product->name,
                    'price' => $product->price,
                    'is_active' => true,
                    // If image, category, etc exist, copy them here. Assuming basic setup.
                ]);
            }
        }
        $b2Products = Product::where('branch_id', 2)->get();

        // 20 Riders for Branch 2
        $riderNames = [
            'Andi Setiawan', 'Budi Santoso', 'Cipto Mangunkusumo', 'Dedi Syahputra', 'Eko Purnomo',
            'Fajar Hidayat', 'Gilang Ramadhan', 'Hendra Wijaya', 'Iwan Fals', 'Joko Anwar',
            'Kiki Amalia', 'Lukman Sardi', 'Maman Abdurrahman', 'Nana Mirdad', 'Oman Rachman',
            'Putra Siregar', 'Qori Akbar', 'Rizky Billar', 'Sule Prikitiew', 'Tukul Arwana'
        ];

        $b2Riders = [];
        foreach ($riderNames as $index => $name) {
            $email = strtolower(str_replace(' ', '', $name)) . '@smraja.com';
            $rider = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'rider',
                    'branch_id' => 2,
                    'whatsapp' => '08' . rand(1000000000, 9999999999)
                ]
            );
            $b2Riders[] = $rider;

            // Create Cart for each rider
            Cart::firstOrCreate(
                ['name' => 'Tether Brew SM Raja #' . str_pad($index + 1, 2, '0', STR_PAD_LEFT)],
                [
                    'user_id' => $rider->id,
                    'status' => 'active',
                    'branch_id' => 2
                ]
            );
        }

        // Get Branch 1 Riders and Products
        $b1Riders = User::where('branch_id', 1)->where('role', 'rider')->get();

        // Admin IDs
        $adminB1 = User::where('branch_id', 1)->where('role', 'admin')->first()->id ?? 42;
        $adminB2 = User::where('branch_id', 2)->where('role', 'admin')->first()->id ?? 46;

        // ==========================================
        // 2. GENERATE DAILY DATA
        // ==========================================
        $this->command->info("Generating daily data from June 1, 2026 to July 7, 2026...");

        $startDate = Carbon::create(2026, 6, 1);
        $endDate = Carbon::create(2026, 7, 7);

        $branchesData = [
            1 => [
                'riders' => $b1Riders,
                'products' => $b1Products,
                'categories' => $b1CategoryIds,
                'admin_id' => $adminB1
            ],
            2 => [
                'riders' => collect($b2Riders),
                'products' => $b2Products,
                'categories' => $b2CategoryIds,
                'admin_id' => $adminB2
            ]
        ];

        $date = $startDate->copy();
        while ($date->lte($endDate)) {
            $isWeekend = $date->isWeekend();
            
            foreach ($branchesData as $bId => $bData) {
                $dailyCashTotal = 0;
                $dailyQrisTotal = 0;
                
                foreach ($bData['riders'] as $rider) {
                    // Decide if rider works today (90% chance)
                    if (rand(1, 100) > 90) continue;

                    // Base cups: 20-50 on weekdays, 30-70 on weekends
                    $baseCups = $isWeekend ? rand(30, 70) : rand(20, 50);
                    
                    $totalGrossIncome = 0;
                    $itemsToCreate = [];
                    
                    // Distribute cups across products
                    $remainingCups = $baseCups;
                    foreach ($bData['products'] as $idx => $product) {
                        if ($remainingCups <= 0) break;
                        
                        // Favor first few products
                        $maxForProduct = ($idx < 3) ? min($remainingCups, rand(10, 25)) : min($remainingCups, rand(0, 10));
                        if ($maxForProduct == 0) continue;
                        
                        $remainingCups -= $maxForProduct;
                        $subtotal = $maxForProduct * $product->price;
                        $totalGrossIncome += $subtotal;
                        
                        $itemsToCreate[] = [
                            'product_id' => $product->id,
                            'stock_out' => $maxForProduct + rand(0, 2), // Sometimes return stock
                            'stock_sold' => $maxForProduct,
                        ];
                    }

                    // Calculate splits
                    $qrisPercentage = rand(20, 40) / 100;
                    $qrisAmount = round($totalGrossIncome * $qrisPercentage / 1000) * 1000;
                    $cashAmount = $totalGrossIncome - $qrisAmount;
                    
                    // Determine if there's a minus (did not setor full amount) - 5% chance
                    $minusAmount = 0;
                    $actualSetor = $cashAmount;
                    if (rand(1, 100) <= 5 && $cashAmount > 10000) {
                        $minusAmount = rand(5000, min(50000, $cashAmount));
                        $actualSetor = $cashAmount - $minusAmount;
                    }

                    // Check if sale already exists
                    $existingSale = RiderDailySale::where('rider_id', $rider->id)->where('date', $date->format('Y-m-d'))->first();
                    
                    if (!$existingSale) {
                        // Create RiderDailySale
                        $sale = RiderDailySale::create([
                            'rider_id' => $rider->id,
                            'branch_id' => $bId,
                            'date' => $date->format('Y-m-d'),
                            'cash_amount' => $cashAmount,
                            'actual_setor' => $actualSetor,
                            'minus_amount' => $minusAmount,
                            'minus_paid' => 0,
                            'minus_status' => $minusAmount > 0 ? 'unpaid' : 'paid',
                            'minus_source' => 'penjualan',
                            'qris_amount' => $qrisAmount,
                            'total_setoran' => $actualSetor + $qrisAmount,
                            'total_gross_income' => $totalGrossIncome,
                            'admin_pemeriksa' => 'Admin System',
                            'admin_id' => $bData['admin_id'],
                        ]);

                        // Create Items
                        foreach ($itemsToCreate as $item) {
                            RiderDailySaleItem::create([
                                'rider_daily_sale_id' => $sale->id,
                                'branch_id' => $bId,
                                'product_id' => $item['product_id'],
                                'stock_out' => $item['stock_out'],
                                'stock_sold' => $item['stock_sold'],
                                'stock_return' => $item['stock_out'] - $item['stock_sold'],
                                'stock_added' => 0,
                            ]);
                        }
                    }

                    $dailyCashTotal += $actualSetor;
                    $dailyQrisTotal += $qrisAmount;

                    // Generate POS Transactions for this rider on this day
                    $cart = Cart::where('user_id', $rider->id)->first();
                    $existingTx = Transaction::where('user_id', $rider->id)->whereDate('created_at', $date->format('Y-m-d'))->first();
                    if ($cart && !$existingTx) {
                        $numTx = rand(3, 8);
                        for ($i=0; $i<$numTx; $i++) {
                            $txProducts = $bData['products']->random(rand(1, 3));
                            $txTotal = 0;
                            $txItems = [];
                            foreach ($txProducts as $txP) {
                                $qty = rand(1, 3);
                                $sub = $qty * $txP->price;
                                $txTotal += $sub;
                                $txItems[] = [
                                    'product_id' => $txP->id,
                                    'qty' => $qty,
                                    'price' => $txP->price,
                                    'subtotal' => $sub
                                ];
                            }
                            
                            $method = rand(1, 100) > 70 ? 'qris' : 'cash';
                            
                            $tx = Transaction::create([
                                'cart_id' => $cart->id,
                                'user_id' => $rider->id,
                                'branch_id' => $bId,
                                'total_price' => $txTotal,
                                'payment_method' => $method,
                                'created_at' => $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59)),
                                'updated_at' => $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59)),
                            ]);

                            foreach ($txItems as $tItem) {
                                TransactionItem::create(array_merge($tItem, ['transaction_id' => $tx->id]));
                            }
                        }
                    }

                    // Rider Finances (Kasbon) - random
                    // Give kasbon 2 times a month on average
                    $existingFinance = RiderFinance::where('rider_id', $rider->id)->where('date', $date->format('Y-m-d'))->first();
                    if (!$existingFinance) {
                        if (rand(1, 100) <= 2) {
                            RiderFinance::create([
                                'rider_id' => $rider->id,
                                'admin_id' => $bData['admin_id'],
                                'branch_id' => $bId,
                                'date' => $date->format('Y-m-d'),
                                'type' => 'kasbon',
                                'amount' => rand(5, 20) * 10000, // 50k - 200k
                                'notes' => 'Kasbon keperluan pribadi'
                            ]);
                        }

                        // Uang Makan - Every Sunday, calculate roughly for the week
                        if ($date->dayOfWeek === Carbon::SUNDAY) {
                            RiderFinance::create([
                                'rider_id' => $rider->id,
                                'admin_id' => $bData['admin_id'],
                                'branch_id' => $bId,
                                'date' => $date->format('Y-m-d'),
                                'type' => 'uang_makan',
                                'amount' => rand(10, 20) * 10000, // 100k - 200k
                                'reference_cups' => rand(200, 350),
                                'notes' => 'Pencairan uang makan mingguan'
                            ]);
                        }
                    }
                } // End rider loop

                // Generate Journals for the day
                if ($dailyCashTotal > 0 && isset($bData['categories']['PENJUALAN CASH'])) {
                    if (!Journal::where('date', $date->format('Y-m-d'))->where('description', 'Total Setoran Cash Penjualan Harian')->where('branch_id', $bId)->exists()) {
                        Journal::create([
                            'date' => $date->format('Y-m-d'),
                            'description' => 'Total Setoran Cash Penjualan Harian',
                            'type' => 'debit',
                            'amount' => $dailyCashTotal,
                            'journal_category_id' => $bData['categories']['PENJUALAN CASH'],
                            'branch_id' => $bId,
                            'created_by' => $bData['admin_id'],
                        ]);
                    }
                }

                if ($dailyQrisTotal > 0 && isset($bData['categories']['PENJUALAN QRIS'])) {
                    if (!Journal::where('date', $date->format('Y-m-d'))->where('description', 'Total Setoran QRIS Penjualan Harian')->where('branch_id', $bId)->exists()) {
                        Journal::create([
                            'date' => $date->format('Y-m-d'),
                            'description' => 'Total Setoran QRIS Penjualan Harian',
                            'type' => 'debit',
                            'amount' => $dailyQrisTotal,
                            'journal_category_id' => $bData['categories']['PENJUALAN QRIS'],
                            'branch_id' => $bId,
                            'created_by' => $bData['admin_id'],
                        ]);
                    }
                }

                // Bahan Baku expenses (Every 3 days)
                if ($date->dayOfYear % 3 == 0 && isset($bData['categories']['BAHAN BAKU'])) {
                    if (!Journal::where('date', $date->format('Y-m-d'))->where('description', 'Belanja Bahan Baku (Susu, Gula, Cup)')->where('branch_id', $bId)->exists()) {
                        Journal::create([
                            'date' => $date->format('Y-m-d'),
                            'description' => 'Belanja Bahan Baku (Susu, Gula, Cup)',
                            'type' => 'credit',
                            'amount' => rand(500000, 1500000),
                            'journal_category_id' => $bData['categories']['BAHAN BAKU'],
                            'branch_id' => $bId,
                            'created_by' => $bData['admin_id'],
                        ]);
                    }
                }
                
                // Lain-lain expenses (Random)
                if (rand(1, 100) <= 10 && isset($bData['categories']['LAIN LAIN'])) {
                    if (!Journal::where('date', $date->format('Y-m-d'))->where('description', 'Beli plastik, kresek, sabun')->where('branch_id', $bId)->exists()) {
                        Journal::create([
                            'date' => $date->format('Y-m-d'),
                            'description' => 'Beli plastik, kresek, sabun',
                            'type' => 'credit',
                            'amount' => rand(50000, 200000),
                            'journal_category_id' => $bData['categories']['LAIN LAIN'],
                            'branch_id' => $bId,
                            'created_by' => $bData['admin_id'],
                        ]);
                    }
                }
            } // End branch loop

            $date->addDay();
        }

        $this->command->info("Data seeding complete!");
    }
}
