<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'cupons.index']);
        Permission::create(['name' => 'cupons.show']);
        Permission::create(['name' => 'cupons.store']);
        Permission::create(['name' => 'cupons.update']);
        Permission::create(['name' => 'cupons.delete']);

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'payroll']);
        $role->givePermissionTo(['cupons.index', 'cupons.show', 'cupons.store', 'cupons.update', 'cupons.delete']);


        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }
}
