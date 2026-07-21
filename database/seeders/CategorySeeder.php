<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public const NAMES = [
        'Elektronik',
        'Fashion',
        'Rumah Tangga',
        'Kesehatan',
        'Makanan & Minuman',
        'Perawatan Diri',
    ];

    public function run(): void
    {
        foreach (self::NAMES as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => 'Kategori produk '.$name.' untuk katalog IntegraMart.',
                    'is_active' => true,
                ]
            );
        }
    }
}
