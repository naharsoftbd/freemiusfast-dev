<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'View',
            'Create',
            'Edit',
            'Delete'
        ];

        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $Adminrole = Role::create(['name' => 'Admin']);
        $Adminrole->givePermissionTo('View');
        $Adminrole->givePermissionTo('Create');
        $Adminrole->givePermissionTo('Edit');
        $Adminrole->givePermissionTo('Delete');

        $Customerrole = Role::create(['name' => 'Customer']);
        $Customerrole->givePermissionTo('View');
        $Customerrole->givePermissionTo('Create');
        $Customerrole->givePermissionTo('Edit');
    }
}
