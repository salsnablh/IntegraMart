<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'order_code' => 'ORD-20260720-0001',
                'customer_email' => 'test@example.com',
                'shipping_cost' => 15000,
                'admin_fee' => 2500,
                'discount_amount' => 0,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_status' => 'not_created',
                'items' => [
                    ['sku' => 'IM-ELC-1001', 'qty' => 2],
                    ['sku' => 'IM-FNB-5001', 'qty' => 1],
                ],
            ],
            [
                'order_code' => 'ORD-20260720-0002',
                'customer_email' => 'rani.wijaya@example.com',
                'shipping_cost' => 20000,
                'admin_fee' => 2500,
                'discount_amount' => 10000,
                'status' => 'paid',
                'payment_status' => 'paid',
                'shipping_status' => 'created',
                'items' => [
                    ['sku' => 'IM-FSN-2001', 'qty' => 3],
                    ['sku' => 'IM-HOM-3001', 'qty' => 1],
                ],
            ],
            [
                'order_code' => 'ORD-20260720-0003',
                'customer_email' => 'budi.santoso@example.com',
                'shipping_cost' => 12000,
                'admin_fee' => 2500,
                'discount_amount' => 0,
                'status' => 'processing',
                'payment_status' => 'paid',
                'shipping_status' => 'not_created',
                'items' => [
                    ['sku' => 'IM-ELC-1001', 'qty' => 1],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $customer = Customer::where('email', $orderData['customer_email'])->firstOrFail();
            $items = collect($orderData['items'])->map(function (array $item) {
                $product = Product::where('sku', $item['sku'])->firstOrFail();

                return [
                    'product' => $product,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['qty'],
                ];
            });

            $subtotal = $items->sum('subtotal');
            $grandTotal = $subtotal + $orderData['shipping_cost'] + $orderData['admin_fee'] - $orderData['discount_amount'];

            $order = Order::updateOrCreate(
                ['order_code' => $orderData['order_code']],
                [
                    'customer_id' => $customer->id,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $orderData['shipping_cost'],
                    'admin_fee' => $orderData['admin_fee'],
                    'discount_amount' => $orderData['discount_amount'],
                    'grand_total' => $grandTotal,
                    'status' => $orderData['status'],
                    'payment_status' => $orderData['payment_status'],
                    'shipping_status' => $orderData['shipping_status'],
                    'recipient_name' => $customer->name,
                    'recipient_phone' => $customer->phone,
                    'shipping_address' => 'Jl. Integrasi No. 20',
                    'shipping_city' => 'Jakarta Selatan',
                    'shipping_province' => 'DKI Jakarta',
                    'shipping_postal_code' => '12560',
                    'notes' => 'Order dummy untuk praktikum IntegraMart.',
                ],
            );

            $items->each(function (array $item) use ($order) {
                OrderItem::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                    ],
                    [
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'subtotal' => $item['subtotal'],
                    ],
                );
            });
        }
    }
}
