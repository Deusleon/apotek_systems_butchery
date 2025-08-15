<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPriceManagementPermissions extends Migration
{
    public function up()
    {
        // Create permissions
        $permissions = [
            'manage price categories',
            'override product prices',
            'view price history',
            'manage default prices'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Assign some permissions to manager role
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'override product prices',
                'view price history'
            ]);
        }
    }

    public function down()
    {
        // Remove permissions
        $permissions = [
            'manage price categories',
            'override product prices',
            'view price history',
            'manage default prices'
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
} 