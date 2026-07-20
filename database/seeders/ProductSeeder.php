<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'IM-ELC-1001',
                'name' => 'Smart LED Bulb 12W',
                'category' => 'Elektronik',
                'description' => 'Lampu pintar hemat energi untuk kebutuhan rumah dan kantor.',
                'price' => 85000,
                'stock' => 45,
                'is_active' => true,
                'image_path' => 'products/smart-led-bulb.jpg',
            ],
            [
                'sku' => 'IM-FSN-2001',
                'name' => 'Kaos Basic Cotton',
                'category' => 'Fashion',
                'description' => 'Kaos katun polos untuk kebutuhan harian.',
                'price' => 75000,
                'stock' => 120,
                'is_active' => true,
                'image_path' => 'products/kaos-basic-cotton.jpg',
            ],
            [
                'sku' => 'IM-HOM-3001',
                'name' => 'Rak Dapur Minimalis',
                'category' => 'Rumah Tangga',
                'description' => 'Rak dapur serbaguna dengan desain ringkas.',
                'price' => 215000,
                'stock' => 18,
                'is_active' => true,
                'image_path' => 'products/rak-dapur-minimalis.jpg',
            ],
            [
                'sku' => 'IM-HLT-4001',
                'name' => 'Vitamin C 500mg',
                'category' => 'Kesehatan',
                'description' => 'Suplemen vitamin C untuk daya tahan tubuh.',
                'price' => 58000,
                'stock' => 0,
                'is_active' => false,
                'image_path' => 'products/vitamin-c-500mg.jpg',
            ],
            [
                'sku' => 'IM-FNB-5001',
                'name' => 'Kopi Arabica 250g',
                'category' => 'Makanan',
                'description' => 'Biji kopi arabica lokal dengan roast medium.',
                'price' => 95000,
                'stock' => 36,
                'is_active' => true,
                'image_path' => 'products/kopi-arabica-250g.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            );
        }
    }
}
