<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions
        $adminPermissions = [
            'manage_products',
            'manage_categories',
            'manage_vendors',
            'manage_orders',
            'manage_users',
            'manage_payments',
            'manage_shipments',
            'view_reports',
            'manage_settings',
            'manage_permissions',
        ];

        $customerPermissions = [
            'place_order',
            'view_own_orders',
            'view_own_profile',
            'manage_own_address',
            'manage_wishlist',
            'manage_cart',
        ];

        $vendorPermissions = [
            'manage_own_products',
            'view_vendor_orders',
            'view_vendor_sales',
            'manage_vendor_profile',
        ];

        // Create permissions
        foreach (array_merge($adminPermissions, $customerPermissions, $vendorPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($adminPermissions);

        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->syncPermissions($customerPermissions);

        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $vendorRole->syncPermissions($vendorPermissions);
    }
}
