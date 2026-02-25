<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user and assign admin role
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@tonercity.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => '+20100000001',
            ]
        );
        $adminUser->assignRole('admin');

        // Create test customer user
        $testCustomer = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password'),
                'phone' => '+20100000002',
            ]
        );
        $testCustomer->assignRole('customer');

        // Create 5 additional customer users
        User::factory()->count(5)->create()->each(function (User $user) {
            $user->assignRole('customer');
        });
    }
}
