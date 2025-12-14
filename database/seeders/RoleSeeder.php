<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage_users',
            'manage_products',
            'view_reports',
            'manage_tax_rates',
            'void_orders',
            'adjust_inventory',
            'create_orders',
            'manage_payees',
            'manage_banks',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and Assign Permissions
        
        // Admin: All permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // Manager: Most permissions except user management and potentially sensitive settings
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'manage_products',
            'view_reports',
            'void_orders',
            'adjust_inventory',
            'create_orders',
            'manage_payees',
            'manage_banks',
        ]);

        // Cashier: Only order creation
        $cashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $cashier->givePermissionTo([
            'create_orders',
        ]);
    }
}
