<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class TaroBrewSeeder extends Seeder
{
    public function run(): void
    {
        // Buat produk Taro Brew jika belum ada
        $taroBrew = Product::firstOrCreate(
            ['name' => 'Taro Brew'],
            [
                'description' => 'Minuman taro premium yang manis dan creamy',
                'price' => 12000,
                'category' => 'non-kopi',
                'image' => 'taro-brew.webp',
            ]
        );

        // Tambahkan inventory Taro Brew ke semua cart yang belum punya
        $carts = Cart::all();
        foreach ($carts as $cart) {
            Inventory::firstOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_id' => $taroBrew->id,
                ],
                [
                    'stock' => rand(5, 30),
                ]
            );
        }

        $this->command->info("Taro Brew berhasil ditambahkan ke {$carts->count()} gerobak.");
    }
}
