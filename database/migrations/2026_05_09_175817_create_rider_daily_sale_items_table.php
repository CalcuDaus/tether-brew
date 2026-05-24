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
        Schema::create('rider_daily_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_daily_sale_id')->constrained('rider_daily_sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('stock_out')->default(0);
            $table->integer('stock_added')->default(0);
            $table->integer('stock_return')->default(0);
            $table->integer('stock_sold')->default(0);
            $table->integer('stock_waste')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_daily_sale_items');
    }
};
