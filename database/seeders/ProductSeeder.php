<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = Category::query()->pluck('id', 'name');

        $products = [
            [
                'sku' => 'IM-ELC-1001',
                'name' => 'Smart LED Bulb 12W',
                'category' => 'Elektronik',
                'category_id' => $categoryMap->get('Elektronik'),
                'description' => 'Lampu pintar hemat energi dengan warna putih netral dan konsumsi daya rendah.',
                'price' => 85000,
                'stock' => 45,
                'is_active' => true,
                'image_path' => 'https://images.unsplash.com/photo-1524484485831-a92ffc0de03f?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'sku' => 'IM-FSN-2001',
                'name' => 'Kaos Basic Cotton',
                'category' => 'Fashion',
                'category_id' => $categoryMap->get('Fashion'),
                'description' => 'Kaos katun combed 30s yang nyaman dipakai untuk aktivitas harian.',
                'price' => 75000,
                'stock' => 120,
                'is_active' => true,
                'image_path' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'sku' => 'IM-HOM-3001',
                'name' => 'Rak Dapur Minimalis',
                'category' => 'Rumah Tangga',
                'category_id' => $categoryMap->get('Rumah Tangga'),
                'description' => 'Rak serbaguna untuk dapur dan ruang penyimpanan dengan desain ringkas.',
                'price' => 215000,
                'stock' => 18,
                'is_active' => true,
                'image_path' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'sku' => 'IM-HLT-4001',
                'name' => 'Vitamin C 500mg',
                'category' => 'Kesehatan',
                'category_id' => $categoryMap->get('Kesehatan'),
                'description' => 'Suplemen vitamin C untuk dukung daya tahan tubuh harian.',
                'price' => 58000,
                'stock' => 0,
                'is_active' => true,
                'image_path' => 'https://images.unsplash.com/photo-1550572017-edd951aa8b58?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'sku' => 'IM-FNB-5001',
                'name' => 'Kopi Arabica 250g',
                'category' => 'Makanan',
                'category_id' => $categoryMap->get('Makanan & Minuman'),
                'description' => 'Biji kopi arabica lokal dengan aroma seimbang dan roast medium.',
                'price' => 95000,
                'stock' => 36,
                'is_active' => true,
                'image_path' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'sku' => 'IM-PRD-6001',
                'name' => 'Sabun Cair Refill 1L',
                'category' => 'Perawatan Diri',
                'category_id' => $categoryMap->get('Perawatan Diri'),
                'description' => 'Sabun cair refill dengan aroma segar dan isi ekonomis.',
                'price' => 42000,
                'stock' => 64,
                'is_active' => true,
                'image_path' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=900&q=80',
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
