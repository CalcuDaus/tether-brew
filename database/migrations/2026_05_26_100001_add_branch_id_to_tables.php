<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tables that will receive a branch_id column.
     * 'conversations' and 'messages' are excluded (global/cross-branch).
     */
    private array $tables = [
        'users',
        'carts',
        'products',
        'inventories',
        'transactions',
        'rider_daily_sales',
        'rider_daily_sale_items',
        'rider_finances',
        'journals',
        'journal_categories',
        'payroll_records',
        'app_settings',
    ];

    public function up(): void
    {
        // Get the default branch id
        $defaultBranchId = DB::table('branches')->where('code', 'utama')->value('id');

        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('branch_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('branches')
                    ->nullOnDelete();
            });

            // Assign all existing records to the default branch
            if ($defaultBranchId) {
                DB::table($table)->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropForeign([$table === 'app_settings' ? 'app_settings_branch_id_foreign' : "{$table}_branch_id_foreign"]);
                $blueprint->dropColumn('branch_id');
            });
        }
    }
};
