<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(999)->after('is_active');
        });

        // Set custom sort order as requested:
        // 1. Aren Brew, 2. Caramel Brew, 3. Butterscotch Brew, 4. Pandan Brew,
        // 5. Vanilla Brew, 6. Matcha, 7. Coklat, 8. Taro, 9. Americano,
        // 10. Kopsu, 11. Americano Apple, 12. Americano Vanilla, 13. Honey Brew
        $order = [
            3  => 1,   // Aren Brew
            5  => 2,   // Caramel Brew
            7  => 3,   // Butterscotch Brew
            4  => 4,   // Pandan Brew
            6  => 5,   // Vanilla Brew
            12 => 6,   // Matcha Brew
            13 => 7,   // Cokelat Brew
            14 => 8,   // Taro Brew
            11 => 9,   // Americano
            8  => 10,  // Kopsu Brew
            10 => 11,  // Americano Apple
            9  => 12,  // Americano Vanilla
            2  => 13,  // Honey Brew
            29 => 14,  // Aren
        ];

        foreach ($order as $id => $sortOrder) {
            DB::table('products')->where('id', $id)->update(['sort_order' => $sortOrder]);
        }

        // Disable Cold Brew (id: 1)
        DB::table('products')->where('id', 1)->update(['is_active' => false]);
    }

    public function down(): void
    {
        // Re-enable Cold Brew
        DB::table('products')->where('id', 1)->update(['is_active' => true]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
