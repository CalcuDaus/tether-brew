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
            $table->decimal('minus_paid', 12, 2)->default(0)->after('minus_amount');
            $table->enum('minus_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('minus_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_daily_sales', function (Blueprint $table) {
            $table->dropColumn(['minus_paid', 'minus_status']);
        });
    }
};
