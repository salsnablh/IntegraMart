<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@integramart.com'],
            [
                'name' => 'Admin IntegraMart',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@integramart.com'],
            [
                'name' => 'Customer IntegraMart',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        Customer::updateOrCreate(
            ['email' => 'customer@integramart.com'],
            [
                'user_id' => $customer->id,
                'name' => 'Customer IntegraMart',
                'phone' => '081200000001',
                'notes' => 'Akun customer dummy bawaan seeder.',
            ]
        );
    }
}
