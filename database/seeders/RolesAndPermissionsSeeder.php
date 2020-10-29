<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET foreign_key_checks=0');
        Permission::truncate();
        Role::truncate();
        DB::table('permission_role')->truncate();
        DB::table('permission_user')->truncate();
        DB::table('role_user')->truncate();
        DB::statement('SET foreign_key_checks=1');

        $permissionNames = [
            'user' => [
                'list', 'create', 'update', 'delete', 'set role', 'update password',
            ],
            'type' => [
                'list', 'create', 'update', 'delete',
            ],
            'color' => [
                'list', 'create', 'update', 'delete',
            ],
            'size' => [
                'list', 'create', 'update', 'delete',
            ],
            'material' => [
                'list', 'create', 'update', 'delete',
            ],
            'item' => [
                'list', 'create', 'update', 'delete',
            ],
            'salesman' => [
                'list', 'create', 'update', 'delete',
            ],
            'customer' => [
                'list', 'create', 'update', 'delete',
            ],
            'role' => [
                'list', 'create', 'update', 'delete',
            ],
            'order' => [
                'list', 'create', 'update', 'delete', 'check',
            ],
            'production' => [
                'list', 'create', 'update', 'delete',
            ],
        ];

        $superAdmin = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super admin',
        ]);

        foreach ($permissionNames as $key => $permissionName) {
            foreach ($permissionName as $value) {
                $name = $key . ' ' . $value;
                $permission = Permission::create([
                    'name' => Str::slug($name, '-'),
                    'display_name' => ucfirst($name),
                ]);

                $superAdmin->attachPermission($permission);
            }
        }

        $users = User::whereIn('username', ['choirool', 'admin'])->get();
        foreach ($users as $user) {
            $user->attachRole($superAdmin);
        }
    }
}
