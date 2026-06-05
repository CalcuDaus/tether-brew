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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        \DB::table('app_settings')->insert([
            ['key' => 'uang_makan_target_cup', 'value' => '1040', 'label' => 'Target Cup Uang Makan (per bulan)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'uang_makan_base_amount', 'value' => '650000', 'label' => 'Base Uang Makan (Rp)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bonus_per_cup', 'value' => '2000', 'label' => 'Bonus per Cup (Rp)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
