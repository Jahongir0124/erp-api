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
            'change-status-user',
            'change-role-user',

            'view-product',
            'create-product',
            'update-product',
            'delete-product',

            'view-category',
            'create-category',
            'update-category',
            'delete-category',

            'view-order',
            'create-order',
            'update-order',
            'delete-order',
            'confirm-order',
            'complete-order',
            'change-order-status',
            'cancel-order-pending',
            'cancel-order-confirmed'
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
        $seller = Role::firstOrCreate([
            'name' => 'seller'
        ]);

        $super_admin->givePermissionTo(Permission::all());
        $manager->syncPermissions([
            'view-category',
            'create-category',
            'update-category',
             'view-user',
             'view-order',
             'create-order',
             'update-order',
             'confirm-order',
             'cancel-order-pending',
             'cancel-order-confirmed',
             'complete-order',
             'change-order-status',
             'view-product'

            
        ]);
        $seller->syncPermissions([
            'view-order',
            'create-order',
            'update-order',
            'cancel-order-pending',
            'view-product'
        ]);

        
    }
}
