<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\JournalCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoJournalSeeder extends Seeder
{
    /**
     * Seed realistic journal entries for demo purposes over the last 30 days.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $owner = User::where('role', 'owner')->first();

        if (!$admin && !$owner) {
            $this->command->warn('No admin or owner found. Run DatabaseSeeder first.');
            return;
        }

        // Get or create categories
        $bahanBaku = JournalCategory::firstOrCreate(['name' => 'BAHAN BAKU']);
        $nonBahanBaku = JournalCategory::firstOrCreate(['name' => 'NON BAHAN BAKU']);

        // Clear existing journal data
        Journal::query()->delete();

        $this->command->info('Generating 30 days of journal entries...');

        // =============================================
        // DEBIT (Income) entries templates
        // =============================================
        $debitTemplates = [
            ['desc' => 'Pendapatan penjualan harian rider', 'min' => 800000, 'max' => 2500000, 'cat' => null, 'freq' => 'daily'],
            ['desc' => 'Setoran QRIS harian', 'min' => 400000, 'max' => 1200000, 'cat' => null, 'freq' => 'daily'],
            ['desc' => 'Penjualan event kampus', 'min' => 500000, 'max' => 1500000, 'cat' => null, 'freq' => 'weekly'],
            ['desc' => 'Pendapatan catering pesanan', 'min' => 300000, 'max' => 800000, 'cat' => null, 'freq' => 'biweekly'],
            ['desc' => 'Bonus kerjasama grab/gojek', 'min' => 200000, 'max' => 500000, 'cat' => null, 'freq' => 'monthly'],
        ];

        // =============================================
        // CREDIT (Expense) entries templates
        // =============================================
        $creditTemplates = [
            // Bahan Baku
            ['desc' => 'Beli kopi bubuk arabika 5kg', 'min' => 350000, 'max' => 500000, 'cat' => $bahanBaku->id, 'freq' => 'weekly'],
            ['desc' => 'Beli susu UHT 24 pack', 'min' => 280000, 'max' => 360000, 'cat' => $bahanBaku->id, 'freq' => 'weekly'],
            ['desc' => 'Beli gula aren 10kg', 'min' => 150000, 'max' => 250000, 'cat' => $bahanBaku->id, 'freq' => 'biweekly'],
            ['desc' => 'Beli sirup vanilla & caramel', 'min' => 120000, 'max' => 200000, 'cat' => $bahanBaku->id, 'freq' => 'biweekly'],
            ['desc' => 'Beli matcha powder 1kg', 'min' => 180000, 'max' => 280000, 'cat' => $bahanBaku->id, 'freq' => 'biweekly'],
            ['desc' => 'Beli cokelat bubuk premium', 'min' => 100000, 'max' => 180000, 'cat' => $bahanBaku->id, 'freq' => 'biweekly'],
            ['desc' => 'Beli madu asli 2 botol', 'min' => 80000, 'max' => 140000, 'cat' => $bahanBaku->id, 'freq' => 'monthly'],
            ['desc' => 'Beli es batu 50 bungkus', 'min' => 100000, 'max' => 200000, 'cat' => $bahanBaku->id, 'freq' => 'weekly'],
            ['desc' => 'Beli cup plastik + tutup 500pcs', 'min' => 200000, 'max' => 350000, 'cat' => $bahanBaku->id, 'freq' => 'biweekly'],
            ['desc' => 'Beli sedotan & tissue', 'min' => 50000, 'max' => 100000, 'cat' => $bahanBaku->id, 'freq' => 'biweekly'],

            // Non Bahan Baku
            ['desc' => 'Bayar listrik bulanan', 'min' => 400000, 'max' => 600000, 'cat' => $nonBahanBaku->id, 'freq' => 'monthly'],
            ['desc' => 'Bayar sewa tempat', 'min' => 1500000, 'max' => 2000000, 'cat' => $nonBahanBaku->id, 'freq' => 'monthly'],
            ['desc' => 'Bayar internet & wifi', 'min' => 200000, 'max' => 350000, 'cat' => $nonBahanBaku->id, 'freq' => 'monthly'],
            ['desc' => 'Service gerobak & peralatan', 'min' => 150000, 'max' => 400000, 'cat' => $nonBahanBaku->id, 'freq' => 'monthly'],
            ['desc' => 'Bensin operasional', 'min' => 50000, 'max' => 100000, 'cat' => $nonBahanBaku->id, 'freq' => 'weekly'],
            ['desc' => 'Beli gas LPG 3kg x 4', 'min' => 80000, 'max' => 120000, 'cat' => $nonBahanBaku->id, 'freq' => 'biweekly'],
            ['desc' => 'Biaya cetak stiker & branding', 'min' => 100000, 'max' => 250000, 'cat' => $nonBahanBaku->id, 'freq' => 'monthly'],
            ['desc' => 'Biaya marketing sosmed', 'min' => 150000, 'max' => 300000, 'cat' => $nonBahanBaku->id, 'freq' => 'monthly'],
            ['desc' => 'Parkir & retribusi', 'min' => 20000, 'max' => 50000, 'cat' => $nonBahanBaku->id, 'freq' => 'weekly'],
        ];

        $entries = [];
        $creators = array_filter([$admin, $owner]);

        for ($dayOffset = 29; $dayOffset >= 0; $dayOffset--) {
            $date = Carbon::today()->subDays($dayOffset);

            // --- DEBIT entries ---
            foreach ($debitTemplates as $tpl) {
                if (!$this->shouldGenerate($tpl['freq'], $date, $dayOffset)) continue;

                $entries[] = [
                    'date'                => $date->toDateString(),
                    'description'         => $tpl['desc'],
                    'type'                => 'debit',
                    'amount'              => $this->roundAmount(rand($tpl['min'], $tpl['max'])),
                    'journal_category_id' => $tpl['cat'],
                    'created_by'          => $creators[array_rand($creators)]->id,
                    'created_at'          => $date->copy()->setTime(rand(7, 10), rand(0, 59)),
                    'updated_at'          => $date->copy()->setTime(rand(7, 10), rand(0, 59)),
                ];
            }

            // --- CREDIT entries ---
            foreach ($creditTemplates as $tpl) {
                if (!$this->shouldGenerate($tpl['freq'], $date, $dayOffset)) continue;

                $entries[] = [
                    'date'                => $date->toDateString(),
                    'description'         => $tpl['desc'],
                    'type'                => 'credit',
                    'amount'              => $this->roundAmount(rand($tpl['min'], $tpl['max'])),
                    'journal_category_id' => $tpl['cat'],
                    'created_by'          => $creators[array_rand($creators)]->id,
                    'created_at'          => $date->copy()->setTime(rand(8, 18), rand(0, 59)),
                    'updated_at'          => $date->copy()->setTime(rand(8, 18), rand(0, 59)),
                ];
            }
        }

        // Bulk insert
        foreach (array_chunk($entries, 50) as $chunk) {
            Journal::insert($chunk);
        }

        $totalEntries = count($entries);
        $totalDebit = collect($entries)->where('type', 'debit')->sum('amount');
        $totalCredit = collect($entries)->where('type', 'credit')->sum('amount');

        $this->command->info("✅ Created {$totalEntries} journal entries.");
        $this->command->info("   Debit:  Rp " . number_format($totalDebit, 0, ',', '.'));
        $this->command->info("   Kredit: Rp " . number_format($totalCredit, 0, ',', '.'));
        $this->command->info("   Saldo:  Rp " . number_format($totalDebit - $totalCredit, 0, ',', '.'));
    }

    /**
     * Determine if an entry should be generated based on frequency.
     */
    private function shouldGenerate(string $freq, Carbon $date, int $dayOffset): bool
    {
        return match ($freq) {
            'daily'    => true,
            'weekly'   => $date->dayOfWeek === Carbon::MONDAY || ($dayOffset === 0), // every Monday + today
            'biweekly' => in_array($date->day, [1, 8, 15, 22]) || ($dayOffset === 0 && rand(1, 3) === 1),
            'monthly'  => $date->day === 1 || ($dayOffset === 0 && rand(1, 2) === 1),
            default    => rand(1, 5) === 1,
        };
    }

    /**
     * Round amount to nearest 5000 for realistic feel.
     */
    private function roundAmount(int $amount): int
    {
        return (int) round($amount / 5000) * 5000;
    }
}
