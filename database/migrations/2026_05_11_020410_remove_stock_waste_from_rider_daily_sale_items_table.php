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
        Schema::table('rider_daily_sale_items', function (Blueprint $table) {
            $table->dropColumn('stock_waste');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_daily_sale_items', function (Blueprint $table) {
            $table->integer('stock_waste')->default(0)->after('stock_sold');
        });
    }
};
