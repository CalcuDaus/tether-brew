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
        Schema::create('rider_daily_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('cash_amount', 12, 2)->default(0);
            $table->decimal('qris_amount', 12, 2)->default(0);
            $table->decimal('total_setoran', 12, 2)->default(0);
            $table->decimal('total_gross_income', 12, 2)->default(0);
            $table->string('admin_pemeriksa')->nullable();
            $table->foreignId('admin_id')->constrained('users');
            $table->timestamps();

            $table->unique(['rider_id', 'date']); // 1 entry per rider per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_daily_sales');
    }
};
