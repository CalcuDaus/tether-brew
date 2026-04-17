<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateProductImagesSeeder extends Seeder
{
    public function run(): void
    {
        $imageMap = [
            'Cold Brew' => 'cold-brew.webp',
            'Honey Brew' => 'honey-brew.webp',
            'Aren Brew' => 'aren-brew.webp',
            'Pandan Brew' => 'pandan-brew.webp',
            'Caramel Brew' => 'caramel-brew.webp',
            'Vanilla Brew' => 'vanilla-brew.webp',
            'Butterscotch Brew' => 'butterscotch-brew.webp',
            'Kopsu Brew' => 'kopsu-brew.webp',
            'Americano Vanilla' => 'americano-vanilla.webp',
            'Americano Apple' => 'americano-apple.webp',
            'Americano' => 'americano.webp',
            'Matcha Brew' => 'matcha-brew.webp',
            'Cokelat Brew' => 'cokelat-brew.webp',
            'Taro Brew' => 'taro-brew.webp',
        ];

        foreach ($imageMap as $name => $image) {
            Product::where('name', $name)->update(['image' => $image]);
        }

        $this->command->info('✅ Product images updated successfully!');
    }
}
