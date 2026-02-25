<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class bootstrap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bootstrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstrap the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // create admin user
        $adminUser = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@tonercity.com',
            'password' => Hash::make('password'),
            'phone' => '+20100000001',
        ]);

        $vendor = Vendor::firstOrCreate([
            'user_id' => $adminUser->id,
            'name' => 'Admin Vendor',
            'slug' => 'admin-vendor',
            'description' => 'System vendor for admin products',
            'commission_rate' => 0.00,
        ]);

        $vendor->save();
    }
}
