<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StockPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions if they don't exist
        $permissions = [
            'manage stock',
            'view stock adjustments',
            'create stock adjustments',
            'approve stock count schedules'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get or create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Assign all permissions to admin role
        $adminRole->givePermissionTo($permissions);

        // Get or create manager role
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        
        // Assign permissions to manager role
        $managerRole->givePermissionTo($permissions);
    }
} 