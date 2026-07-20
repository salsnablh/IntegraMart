<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'sku' => strtoupper(fake()->bothify('IM-???-####')),
            'name' => Str::title($name),
            'category' => fake()->randomElement(['Elektronik', 'Fashion', 'Rumah Tangga', 'Kesehatan', 'Makanan']),
            'description' => fake()->sentence(12),
            'price' => fake()->randomFloat(2, 15000, 2500000),
            'stock' => fake()->numberBetween(0, 120),
            'is_active' => fake()->boolean(85),
            'image_path' => null,
        ];
    }
}
