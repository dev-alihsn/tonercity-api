<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class AdminVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the admin user
        $adminUser = User::where('email', 'admin@tonercity.com')->first();

        if ($adminUser) {
            // Create admin vendor if it doesn't exist
            Vendor::firstOrCreate(
                ['slug' => 'admin-vendor'],
                [
                    'user_id' => $adminUser->id,
                    'name' => 'Admin Vendor',
                    'description' => 'System vendor for admin products',
                    'logo_id' => null,
                    'is_active' => true,
                    'commission_rate' => 0.00,
                ]
            );
        }
    }
}
