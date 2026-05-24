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
            $table->decimal('actual_setor', 12, 2)->default(0)->after('cash_amount');
            $table->decimal('minus_amount', 12, 2)->default(0)->after('actual_setor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_daily_sales', function (Blueprint $table) {
            $table->dropColumn(['actual_setor', 'minus_amount']);
        });
    }
};
