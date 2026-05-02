<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->unique()->after('whatsapp');
        });

        // Alter enum role to add 'customer'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','admin','rider','customer') DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','admin','rider') DEFAULT 'rider'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
