<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::statement("SET foreign_key_checks=0");
        Permission::truncate();
        Role::truncate();
        DB::statement("SET foreign_key_checks=1");
        
        $permissionNames = [
            'list user', 'create user', 'update user', 'delete user', 'set role user',
            'list type','create type','update type','delete type',
            'list color', 'create color', 'update color', 'delete color',
            'list size', 'create size', 'update size', 'delete size',
            'list material', 'create material', 'update material', 'delete material',
            'list item', 'create item', 'update item', 'delete item',
            'list salesman', 'create salesman', 'update salesman', 'delete salesman',
            'list customer', 'create customer', 'update customer', 'delete customer',
            'list role', 'create role', 'update role', 'delete role',
            'list order', 'create order', 'update order', 'delete order', 'check order', 
            'list production', 'create production', 'update production', 'delete production',
        ];

        $permissions = collect($permissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'sanctum'];
        });

        Permission::insert($permissions->toArray());

        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }
}
