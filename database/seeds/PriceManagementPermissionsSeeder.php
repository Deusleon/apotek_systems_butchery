<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PriceManagementPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions if they don't exist
        $permissions = [
            'manage price categories',
            'override product prices',
            'view price history',
            'manage default prices'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'override product prices',
                'view price history'
            ]);
        }

        // Create a new role for price management if it doesn't exist
        $pricingManagerRole = Role::firstOrCreate(['name' => 'pricing_manager']);
        $pricingManagerRole->givePermissionTo([
            'manage price categories',
            'override product prices',
            'view price history',
            'manage default prices'
        ]);
    }
} 