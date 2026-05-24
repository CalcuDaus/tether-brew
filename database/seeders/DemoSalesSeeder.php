<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\RiderDailySale;
use App\Models\RiderDailySaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSalesSeeder extends Seeder
{
    /**
     * Seed realistic demo sales data for ALL riders over the last 30 days.
     * Creates varied, believable patterns so dashboard charts look impressive.
     */
    public function run(): void
    {
        $riders = User::where('role', 'rider')->get();
        $products = Product::all();
        $admin = User::where('role', 'admin')->first()
                 ?? User::where('role', 'owner')->first();

        if ($riders->isEmpty() || $products->isEmpty() || !$admin) {
            $this->command->warn('Missing riders, products, or admin. Run DatabaseSeeder first.');
            return;
        }

        // Clear existing daily sales to avoid duplicate key conflicts
        RiderDailySale::query()->delete();

        $this->command->info("Generating 30 days of sales data for {$riders->count()} riders...");

        // Assign each rider a "performance tier" for realistic variation
        // Tier 1 (top): ~15% riders — sell 40-70 cups/day
        // Tier 2 (good): ~35% riders — sell 25-45 cups/day
        // Tier 3 (avg):  ~35% riders — sell 10-30 cups/day
        // Tier 4 (low):  ~15% riders — sell 3-15 cups/day
        $tiers = [];
        $riderCount = $riders->count();
        foreach ($riders as $i => $rider) {
            $pct = ($i + 1) / $riderCount;
            if ($pct <= 0.15) {
                $tiers[$rider->id] = ['min' => 40, 'max' => 70, 'skip_chance' => 0.05]; // almost never skip
            } elseif ($pct <= 0.50) {
                $tiers[$rider->id] = ['min' => 25, 'max' => 45, 'skip_chance' => 0.10];
            } elseif ($pct <= 0.85) {
                $tiers[$rider->id] = ['min' => 10, 'max' => 30, 'skip_chance' => 0.20];
            } else {
                $tiers[$rider->id] = ['min' => 3, 'max' => 15, 'skip_chance' => 0.35];
            }
        }

        // Product popularity weights (higher = more likely to be sold)
        $productWeights = [];
        foreach ($products as $product) {
            // Premium products slightly less popular, cheap ones more popular
            $weight = match (true) {
                $product->price >= 15000 => rand(6, 10),
                $product->price >= 12000 => rand(8, 14),
                $product->price >= 10000 => rand(10, 16),
                default                  => rand(12, 18),
            };
            $productWeights[$product->id] = $weight;
        }

        $bar = $this->command->getOutput()->createProgressBar($riders->count());
        $bar->start();

        foreach ($riders as $rider) {
            $tier = $tiers[$rider->id];

            for ($dayOffset = 29; $dayOffset >= 0; $dayOffset--) {
                $date = Carbon::today()->subDays($dayOffset);

                // Weekend boost (Sat/Sun sell ~20% more)
                $weekendBoost = in_array($date->dayOfWeek, [0, 6]) ? 1.20 : 1.0;

                // Random skip day (rider didn't work)
                if (mt_rand(1, 100) / 100 <= $tier['skip_chance']) {
                    continue;
                }

                // Determine total cups for this day
                $baseCups = rand($tier['min'], $tier['max']);
                $totalCups = (int) round($baseCups * $weekendBoost);

                // Distribute cups across products using weighted random
                $productSales = $this->distributeToProducts($products, $productWeights, $totalCups);

                // Calculate money totals
                $totalGross = 0;
                foreach ($productSales as $pid => $sold) {
                    $product = $products->firstWhere('id', $pid);
                    $totalGross += $product->price * $sold;
                }

                // Payment split: ~55-65% cash, rest QRIS (realistic for street vendors)
                $cashPct = rand(45, 70) / 100;
                $cashAmount = round($totalGross * $cashPct, 2);
                $qrisAmount = round($totalGross - $cashAmount, 2);

                // Setoran (deposit) — usually matches cash, sometimes slightly less (minus)
                $minusChance = rand(1, 100);
                $minusAmount = 0;
                if ($minusChance <= 8) { // 8% chance of minus
                    $minusAmount = rand(1, 3) * 5000; // 5k, 10k, or 15k short
                }
                $actualSetor = $cashAmount - $minusAmount;

                $sale = RiderDailySale::create([
                    'rider_id'         => $rider->id,
                    'date'             => $date->toDateString(),
                    'cash_amount'      => $cashAmount,
                    'actual_setor'     => max(0, $actualSetor),
                    'minus_amount'     => $minusAmount,
                    'qris_amount'      => $qrisAmount,
                    'total_setoran'    => max(0, $actualSetor) + $qrisAmount,
                    'total_gross_income' => $totalGross,
                    'admin_pemeriksa'  => $admin->name,
                    'admin_id'         => $admin->id,
                ]);

                // Create sale items
                foreach ($productSales as $productId => $sold) {
                    if ($sold <= 0) continue;

                    $stockOut = $sold + rand(0, 3); // took slightly more than sold
                    $stockReturn = $stockOut - $sold;

                    RiderDailySaleItem::create([
                        'rider_daily_sale_id' => $sale->id,
                        'product_id'          => $productId,
                        'stock_out'           => $stockOut,
                        'stock_added'         => 0,
                        'stock_return'        => $stockReturn,
                        'stock_sold'          => $sold,
                    ]);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        $totalSales = RiderDailySale::count();
        $totalItems = RiderDailySaleItem::count();
        $this->command->info("✅ Created {$totalSales} daily sales records with {$totalItems} item entries.");
    }

    /**
     * Distribute a target number of cups across products using weighted random selection.
     */
    private function distributeToProducts($products, array $weights, int $totalCups): array
    {
        $sales = [];
        foreach ($products as $p) {
            $sales[$p->id] = 0;
        }

        // Pick 4-8 products this rider sells today (not all products every day)
        $activeProductCount = min($products->count(), rand(4, min(8, $products->count())));
        $activeProducts = $products->random($activeProductCount);

        // Build weighted pool from active products only
        $pool = [];
        foreach ($activeProducts as $p) {
            for ($w = 0; $w < ($weights[$p->id] ?? 5); $w++) {
                $pool[] = $p->id;
            }
        }

        if (empty($pool)) return $sales;

        // Distribute cups
        for ($i = 0; $i < $totalCups; $i++) {
            $pid = $pool[array_rand($pool)];
            $sales[$pid]++;
        }

        return $sales;
    }
}
