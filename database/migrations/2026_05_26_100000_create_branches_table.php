<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // "Cabang Semarang", "Cabang Solo"
            $table->string('code')->unique();    // "smg", "solo" — untuk URL/identifier
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default branch so existing data can be assigned
        DB::table('branches')->insert([
            'name' => 'Cabang Utama',
            'code' => 'utama',
            'address' => null,
            'phone' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
