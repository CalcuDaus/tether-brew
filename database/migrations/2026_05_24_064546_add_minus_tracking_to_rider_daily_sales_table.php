<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rider_daily_sales', function (Blueprint $table) {
            $table->enum('minus_source', ['penjualan', 'carry_over'])->default('penjualan')->after('minus_amount');
            $table->text('minus_notes')->nullable()->after('minus_source');
            $table->unsignedBigInteger('carry_over_from_payroll_id')->nullable()->after('minus_notes');
            $table->foreign('carry_over_from_payroll_id')->references('id')->on('payroll_records')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_daily_sales', function (Blueprint $table) {
            $table->dropForeign(['carry_over_from_payroll_id']);
            $table->dropColumn(['minus_source', 'minus_notes', 'carry_over_from_payroll_id']);
        });
    }
};
