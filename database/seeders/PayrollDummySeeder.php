<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AppSetting;
use App\Models\PayrollRecord;
use App\Models\RiderDailySale;
use App\Models\RiderFinance;
use Carbon\Carbon;

class PayrollDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Pastikan Settings ada
        AppSetting::setValue('uang_makan_target_cup', 1040);
        AppSetting::setValue('uang_makan_base_amount', 650000);
        AppSetting::setValue('bonus_per_cup', 2000);

        // 2. Ambil Rider dan Admin
        $rider = User::where('role', 'rider')->first();
        $admin = User::where('role', 'admin')->first();

        if (!$rider || !$admin) {
            $this->command->warn('Rider atau Admin tidak ditemukan. Silakan seed user terlebih dahulu.');
            return;
        }

        // 3. Buat Slip Gaji LUNAS (Confirmed) - Minggu Lalu
        $lastWeekStart = Carbon::now()->subWeeks(1)->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeeks(1)->endOfWeek();

        $confirmedPayroll = PayrollRecord::create([
            'rider_id' => $rider->id,
            'admin_id' => $admin->id,
            'type' => 'weekly',
            'period_start' => $lastWeekStart->format('Y-m-d'),
            'period_end' => $lastWeekEnd->format('Y-m-d'),
            'total_cups' => 350,
            'gross_income' => 350 * 2000,
            'kasbon_outstanding' => 100000,
            'kasbon_deducted' => 50000,
            'minus_outstanding' => 25000,
            'minus_deducted' => 25000,
            'uang_makan_adjustment' => 0,
            'net_income' => (350 * 2000) - 50000 - 25000,
            'notes' => 'Gaji minggu lalu (Dummy Lunas)',
            'status' => 'confirmed',
            'confirmed_at' => Carbon::now()->subDays(3),
            'confirmed_by' => $admin->id,
        ]);

        // Karena kasbon ada sisa (outstanding 100k - deducted 50k = 50k), kita buat finance data dummy
        // supaya logic kasbon match
        RiderFinance::create([
            'rider_id' => $rider->id,
            'admin_id' => $admin->id,
            'date' => $lastWeekStart->copy()->addDays(2)->format('Y-m-d'),
            'type' => 'kasbon',
            'amount' => 100000,
            'notes' => 'Kasbon Dummy untuk test gaji',
        ]);

        // 4. Buat Slip Gaji DRAFT (Belum Dibayar) - Minggu Ini
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisWeekEnd = Carbon::now()->endOfWeek();

        // outstanding kasbon otomatis sisa 50k dari minggu lalu
        $draftPayroll = PayrollRecord::create([
            'rider_id' => $rider->id,
            'admin_id' => $admin->id,
            'type' => 'weekly',
            'period_start' => $thisWeekStart->format('Y-m-d'),
            'period_end' => $thisWeekEnd->format('Y-m-d'),
            'total_cups' => 400,
            'gross_income' => 400 * 2000,
            'kasbon_outstanding' => 50000, // Sisa dari minggu lalu
            'kasbon_deducted' => 50000, // Rencana dilunasin
            'minus_outstanding' => 0,
            'minus_deducted' => 0,
            'uang_makan_adjustment' => 0,
            'net_income' => (400 * 2000) - 50000,
            'notes' => 'Gaji minggu ini (Dummy Draft)',
            'status' => 'draft',
        ]);

        $this->command->info('Dummy Payroll records (Draft & Confirmed) seeded successfully!');
    }
}
