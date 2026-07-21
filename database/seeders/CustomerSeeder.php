<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $loginUser = User::where('email', 'customer@integramart.com')->firstOrFail();

        Customer::updateOrCreate(
            ['email' => 'customer@integramart.com'],
            [
                'user_id' => $loginUser->id,
                'name' => 'Customer IntegraMart',
                'phone' => '081200000001',
                'notes' => 'Customer yang terhubung dengan user login dummy.',
            ],
        );

        $customers = [
            [
                'name' => 'Rani Wijaya',
                'email' => 'rani.wijaya@example.com',
                'phone' => '081298765432',
                'notes' => 'Customer retail reguler.',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'phone' => '082112345678',
                'notes' => 'Customer dengan minat produk elektronik.',
            ],
            [
                'name' => 'Maya Lestari',
                'email' => 'maya.lestari@example.com',
                'phone' => '085677889900',
                'notes' => 'Customer baru dari kampanye promosi.',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                ['user_id' => null] + $customer,
            );
        }
    }
}
