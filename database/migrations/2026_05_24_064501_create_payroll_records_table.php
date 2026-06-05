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
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users');
            $table->enum('type', ['weekly', 'monthly', 'custom']);
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('total_cups')->default(0);
            $table->decimal('gross_income', 12, 2)->default(0);
            $table->decimal('kasbon_outstanding', 12, 2)->default(0);
            $table->decimal('kasbon_deducted', 12, 2)->default(0);
            $table->decimal('minus_outstanding', 12, 2)->default(0);
            $table->decimal('minus_deducted', 12, 2)->default(0);
            $table->decimal('uang_makan_adjustment', 12, 2)->default(0);
            $table->decimal('net_income', 12, 2)->default(0);
            $table->enum('status', ['draft', 'confirmed'])->default('draft');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
    }
};
