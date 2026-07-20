<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 4);
        $price = fake()->randomFloat(2, 15000, 500000);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'qty' => $qty,
            'price' => $price,
            'subtotal' => $qty * $price,
        ];
    }
}
