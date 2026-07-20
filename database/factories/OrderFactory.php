<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_code' => 'ORD-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'customer_id' => Customer::factory(),
            'subtotal' => 0,
            'shipping_cost' => fake()->randomElement([10000, 15000, 20000, 25000]),
            'admin_fee' => 2500,
            'discount_amount' => 0,
            'grand_total' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_status' => 'not_created',
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->phoneNumber(),
            'shipping_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_province' => fake()->state(),
            'shipping_postal_code' => fake()->postcode(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
