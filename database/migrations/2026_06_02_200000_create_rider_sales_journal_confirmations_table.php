<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_sales_journal_confirmations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('branch_id');
            $table->decimal('total_cash', 15, 2)->default(0);
            $table->decimal('total_qris', 15, 2)->default(0);
            $table->decimal('total_minus', 15, 2)->default(0);
            $table->decimal('total_omset', 15, 2)->default(0);
            $table->integer('rider_count')->default(0);
            $table->unsignedBigInteger('confirmed_by');
            $table->timestamps();

            $table->unique(['date', 'branch_id']);
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_sales_journal_confirmations');
    }
};
