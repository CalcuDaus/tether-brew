<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartLocation;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // USERS — Owner & Admin
        // ==========================================
        $owner = User::create([
            'name' => 'Eki Owner',
            'email' => 'ekitether@gmail.com',
            'password' => Hash::make('Eki@123!@#'),
            'role' => 'owner',
        ]);

        $admin = User::create([
            'name' => 'Sarah Admin',
            'email' => 'sarahtether@gmail.com',
            'password' => Hash::make('Sarah@123!@#'),
            'role' => 'admin',
        ]);

        // ==========================================
        // USERS — 39 Riders (random names)
        // ==========================================
        $firstNames = [
            'Andi', 'Budi', 'Cahyo', 'Dimas', 'Eko', 'Fajar', 'Galih', 'Hendra',
            'Ilham', 'Joko', 'Kurniawan', 'Lutfi', 'Maulana', 'Naufal', 'Oscar',
            'Pratama', 'Qadri', 'Rizky', 'Surya', 'Taufik', 'Umar', 'Vino',
            'Wahyu', 'Xander', 'Yusuf', 'Zaki', 'Agung', 'Bagas', 'Candra',
            'Dani', 'Erwin', 'Firman', 'Gilang', 'Hafiz', 'Irfan', 'Jefri',
            'Kevin', 'Lukman', 'Mansyur',
        ];

        $lastNames = [
            'Pratama', 'Saputra', 'Wijaya', 'Nugroho', 'Hidayat', 'Santoso',
            'Ramadhan', 'Permana', 'Kurniawan', 'Siregar', 'Nasution', 'Harahap',
            'Lubis', 'Tarigan', 'Sembiring', 'Ginting', 'Situmorang', 'Simanjuntak',
            'Panjaitan', 'Hutabarat',
        ];

        $riders = [];
        for ($i = 1; $i <= 39; $i++) {
            $number = str_pad($i, 2, '0', STR_PAD_LEFT);
            $name = $firstNames[$i - 1] . ' ' . $lastNames[array_rand($lastNames)];

            $riders[] = User::create([
                'name' => $name,
                'email' => "tether{$number}@tether.com",
                'whatsapp' => '628' . rand(1000000000, 9999999999),
                'password' => Hash::make('password'),
                'role' => 'rider',
            ]);
        }

        // ==========================================
        // PRODUCTS (Tether Brew Menu)
        // ==========================================
        $products = collect([
            // Coffee
            Product::create(['name' => 'Cold Brew', 'description' => 'Kopi cold brew premium yang diekstrak dingin selama 12 jam', 'price' => 15000, 'category' => 'kopi']),
            Product::create(['name' => 'Honey Brew', 'description' => 'Kopi dengan campuran madu asli pilihan', 'price' => 15000, 'category' => 'kopi']),
            Product::create(['name' => 'Aren Brew', 'description' => 'Kopi susu dengan gula aren nusantara', 'price' => 12000, 'category' => 'kopi']),
            Product::create(['name' => 'Pandan Brew', 'description' => 'Kopi dengan aroma pandan yang khas dan segar', 'price' => 12000, 'category' => 'kopi']),
            Product::create(['name' => 'Caramel Brew', 'description' => 'Kopi dengan karamel manis yang creamy', 'price' => 12000, 'category' => 'kopi']),
            Product::create(['name' => 'Vanilla Brew', 'description' => 'Kopi dengan vanilla yang lembut dan harum', 'price' => 12000, 'category' => 'kopi']),
            Product::create(['name' => 'Butterscotch Brew', 'description' => 'Kopi dengan butterscotch yang gurih manis', 'price' => 12000, 'category' => 'kopi']),
            Product::create(['name' => 'Kopsu Brew', 'description' => 'Kopi susu klasik ala Tether Brew', 'price' => 10000, 'category' => 'kopi']),
            Product::create(['name' => 'Americano Vanilla', 'description' => 'Americano dengan sentuhan vanilla segar', 'price' => 10000, 'category' => 'kopi']),
            Product::create(['name' => 'Americano Apple', 'description' => 'Americano dengan rasa apel yang unik', 'price' => 10000, 'category' => 'kopi']),
            Product::create(['name' => 'Americano', 'description' => 'Espresso murni dengan air panas', 'price' => 8000, 'category' => 'kopi']),

            // Non-Coffee
            Product::create(['name' => 'Matcha Brew', 'description' => 'Green tea matcha premium yang creamy', 'price' => 12000, 'category' => 'non-kopi']),
            Product::create(['name' => 'Cokelat Brew', 'description' => 'Cokelat premium yang rich dan lembut', 'price' => 12000, 'category' => 'non-kopi']),
            Product::create(['name' => 'Taro Brew', 'description' => 'Minuman taro premium yang manis dan creamy', 'price' => 12000, 'category' => 'non-kopi', 'image' => 'taro-brew.webp']),
        ]);

        // ==========================================
        // CARTS — 39 Gerobak (semua nonaktif)
        // ==========================================
        $areas = [
            'USU', 'STMIK Triguna Dharma', 'Cadika Medan Johor', 'Medan Sunggal',
            'Medan Polonia', 'Medan Helvetia', 'Medan Kota', 'Medan Timur',
            'Medan Baru', 'Medan Selayang', 'Medan Tuntungan', 'Medan Amplas',
            'Medan Denai', 'Medan Area', 'Medan Perjuangan', 'Medan Tembung',
            'Medan Marelan', 'Medan Labuhan', 'Medan Belawan', 'Medan Deli',
            'Medan Petisah', 'Medan Maimun', 'Padang Bulan', 'Setia Budi',
            'Jalan Gatot Subroto', 'Jalan Sisingamangaraja', 'Jalan Jamin Ginting',
            'Jalan Adam Malik', 'Jalan Ringroad', 'Jalan SM Raja', 'Jalan Krakatau',
            'Jalan Sutomo', 'Jalan Iskandar Muda', 'Jalan Djamin Ginting',
            'Jalan Kapten Muslim', 'Jalan Sei Batang Hari', 'Komplek MMTC',
            'Komplek Cemara Asri', 'Tanjung Morawa',
        ];

        // Koordinat dasar Medan dengan variasi
        $baseLat = 3.5700;
        $baseLng = 98.6600;

        $carts = [];
        for ($i = 0; $i < 39; $i++) {
            $number = str_pad($i + 1, 2, '0', STR_PAD_LEFT);

            $cart = Cart::create([
                'name' => "Tether Brew #{$number}",
                'description' => "Gerobak Tether Brew area {$areas[$i]}",
                'user_id' => $riders[$i]->id,
                'status' => 'inactive',
            ]);

            // Lokasi tersebar di area Medan
            $lat = $baseLat + (rand(-400, 400) / 10000);
            $lng = $baseLng + (rand(-400, 400) / 10000);

            CartLocation::create([
                'cart_id' => $cart->id,
                'latitude' => round($lat, 4),
                'longitude' => round($lng, 4),
            ]);

            $carts[] = $cart;
        }

        // ==========================================
        // INVENTORIES (stock per gerobak)
        // ==========================================
        foreach ($carts as $cart) {
            foreach ($products as $product) {
                Inventory::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'stock' => rand(5, 30),
                ]);
            }
        }

        // ==========================================
        // SAMPLE TRANSACTIONS (beberapa gerobak saja)
        // ==========================================
        $sampleCarts = array_slice($carts, 0, 6);
        foreach ($sampleCarts as $index => $cart) {
            $this->createSampleTransactions($cart, $riders[$index], $products);
        }
    }

    private function createSampleTransactions(Cart $cart, User $rider, $products): void
    {
        // Create transactions over the last 7 days
        for ($day = 6; $day >= 0; $day--) {
            $transCount = rand(3, 8);
            for ($t = 0; $t < $transCount; $t++) {
                $itemCount = rand(1, 4);
                $selectedProducts = $products->random($itemCount);
                $totalPrice = 0;
                $items = [];

                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 3);
                    $subtotal = $product->price * $qty;
                    $totalPrice += $subtotal;
                    $items[] = [
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ];
                }

                $transaction = Transaction::create([
                    'cart_id' => $cart->id,
                    'user_id' => $rider->id,
                    'total_price' => $totalPrice,
                    'payment_method' => rand(0, 1) ? 'cash' : 'qris',
                    'notes' => null,
                    'created_at' => now()->subDays($day)->setTime(rand(6, 21), rand(0, 59)),
                    'updated_at' => now()->subDays($day)->setTime(rand(6, 21), rand(0, 59)),
                ]);

                foreach ($items as $item) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        ...$item,
                    ]);
                }
            }
        }
    }
}
