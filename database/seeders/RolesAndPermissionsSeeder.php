<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-user',
            'create-user',
            'update-user',
            'delete-user',

            'view-product',
            'create-product',
            'update-product',
            'delete-product',

            'view-category',
            'create-category',
            'update-category',
            'delete-category'
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }


        $super_admin = Role::firstOrCreate([
            'name' => 'super-admin'
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'manager'
        ]);

        $super_admin->givePermissionTo(Permission::all());
        $manager->givePermissionTo([
            'view-category',
            'create-category',
            'update-category'
        ]);

        
    }
}
