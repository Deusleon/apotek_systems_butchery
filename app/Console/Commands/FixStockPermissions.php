<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FixStockPermissions extends Command
{
    protected $signature = 'permissions:fix-stock';
    protected $description = 'Fix stock management permissions';

    public function handle()
    {
        $this->info('Fixing stock management permissions...');

        // Create permissions if they don't exist
        $permissions = [
            'manage stock',
            'view stock',
            'view stock adjustments',
            'create stock adjustments'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->info("Permission '{$permission}' created or verified.");
        }

        // Get or create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->info('Admin role verified.');

        // Assign all permissions to admin role
        $adminRole->givePermissionTo($permissions);
        $this->info('Permissions assigned to admin role.');

        // Get all users with admin role
        $adminUsers = \App\User::role('admin')->get();
        foreach ($adminUsers as $user) {
            $user->syncPermissions($permissions);
            $this->info("Permissions synced for admin user: {$user->name}");
        }

        $this->info('Stock permissions fixed successfully!');
    }
} 