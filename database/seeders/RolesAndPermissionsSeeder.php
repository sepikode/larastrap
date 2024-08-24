<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'users-viewany']);
        Permission::create(['name' => 'users-create']);
        Permission::create(['name' => 'users-view']);
        Permission::create(['name' => 'users-edit']);
        Permission::create(['name' => 'users-delete']);
        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo('edit articles');


        $role = Role::create(['name' => 'sepikode']);
        $role->givePermissionTo(Permission::all());
    }
}
