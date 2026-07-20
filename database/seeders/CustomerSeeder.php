<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $loginUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        Customer::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'user_id' => $loginUser->id,
                'name' => 'Test User',
                'phone' => '081234567890',
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
