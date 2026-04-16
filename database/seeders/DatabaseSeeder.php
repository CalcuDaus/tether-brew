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
        // USERS
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

        $rider1 = User::create([
            'name' => 'Andi Rider',
            'email' => 'rider1@coffee.com',
            'whatsapp' => '6281234567891',
            'password' => Hash::make('password'),
            'role' => 'rider',
        ]);

        $rider2 = User::create([
            'name' => 'Rudi Rider',
            'email' => 'rider2@coffee.com',
            'whatsapp' => '6281234567892',
            'password' => Hash::make('password'),
            'role' => 'rider',
        ]);

        $rider3 = User::create([
            'name' => 'Dimas Rider',
            'email' => 'rider3@coffee.com',
            'whatsapp' => '6281234567893',
            'password' => Hash::make('password'),
            'role' => 'rider',
        ]);

        $rider4 = User::create([
            'name' => 'Fajar Rider',
            'email' => 'rider4@coffee.com',
            'whatsapp' => '6281234567894',
            'password' => Hash::make('password'),
            'role' => 'rider',
        ]);

        $rider5 = User::create([
            'name' => 'Rizky Rider',
            'email' => 'rider5@coffee.com',
            'whatsapp' => '6281234567895',
            'password' => Hash::make('password'),
            'role' => 'rider',
        ]);

        $rider6 = User::create([
            'name' => 'Hendra Rider',
            'email' => 'rider6@coffee.com',
            'whatsapp' => '6281234567896',
            'password' => Hash::make('password'),
            'role' => 'rider',
        ]);

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
            Product::create(['name' => 'Taro Brew', 'description' => 'Minuman taro premium yang manis dan creamy', 'price' => 12000, 'category' => 'non-kopi']),
        ]);

        // ==========================================
        // CARTS (6 Gerobak di area Medan)
        // ==========================================
        $cart1 = Cart::create([
            'name' => 'Tether Brew #01',
            'description' => 'Gerobak Tether Brew area USU (Universitas Sumatera Utara)',
            'user_id' => $rider1->id,
            'status' => 'active',
        ]);

        $cart2 = Cart::create([
            'name' => 'Tether Brew #02',
            'description' => 'Gerobak Tether Brew area STMIK Triguna Dharma',
            'user_id' => $rider2->id,
            'status' => 'active',
        ]);

        $cart3 = Cart::create([
            'name' => 'Tether Brew #03',
            'description' => 'Gerobak Tether Brew area Cadika Medan Johor',
            'user_id' => $rider3->id,
            'status' => 'active',
        ]);

        $cart4 = Cart::create([
            'name' => 'Tether Brew #04',
            'description' => 'Gerobak Tether Brew area Medan Sunggal',
            'user_id' => $rider4->id,
            'status' => 'active',
        ]);

        $cart5 = Cart::create([
            'name' => 'Tether Brew #05',
            'description' => 'Gerobak Tether Brew area Medan Polonia',
            'user_id' => $rider5->id,
            'status' => 'active',
        ]);

        $cart6 = Cart::create([
            'name' => 'Tether Brew #06',
            'description' => 'Gerobak Tether Brew area Medan Helvetia',
            'user_id' => $rider6->id,
            'status' => 'active',
        ]);

        // ==========================================
        // LOCATIONS (Medan area)
        // ==========================================
        CartLocation::create(['cart_id' => $cart1->id, 'latitude' => 3.5652, 'longitude' => 98.6565]);  // USU (Universitas Sumatera Utara)
        CartLocation::create(['cart_id' => $cart2->id, 'latitude' => 3.5870, 'longitude' => 98.6518]);  // STMIK Triguna Dharma
        CartLocation::create(['cart_id' => $cart3->id, 'latitude' => 3.5735, 'longitude' => 98.6895]);  // Cadika Medan Johor
        CartLocation::create(['cart_id' => $cart4->id, 'latitude' => 3.5423, 'longitude' => 98.6192]);  // Medan Sunggal
        CartLocation::create(['cart_id' => $cart5->id, 'latitude' => 3.5590, 'longitude' => 98.6840]);  // Medan Polonia
        CartLocation::create(['cart_id' => $cart6->id, 'latitude' => 3.5982, 'longitude' => 98.6410]);  // Medan Helvetia

        // ==========================================
        // INVENTORIES (stock per gerobak)
        // ==========================================
        foreach ([$cart1, $cart2, $cart3, $cart4, $cart5, $cart6] as $cart) {
            foreach ($products as $product) {
                Inventory::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'stock' => rand(5, 30),
                ]);
            }
        }

        // ==========================================
        // SAMPLE TRANSACTIONS
        // ==========================================
        $this->createSampleTransactions($cart1, $rider1, $products);
        $this->createSampleTransactions($cart2, $rider2, $products);
        $this->createSampleTransactions($cart3, $rider3, $products);
        $this->createSampleTransactions($cart4, $rider4, $products);
        $this->createSampleTransactions($cart5, $rider5, $products);
        $this->createSampleTransactions($cart6, $rider6, $products);
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

