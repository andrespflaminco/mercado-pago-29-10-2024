<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//importar los modelos
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //creación de usuarios
        User::create([
            'name' => 'Flaminco',
            'profile' => 'Admin',
            'status' => 'Active',
            'email' => 'flaminco@flaminco.com',
            'password' => bcrypt('flaminco2021')
        ]);
        User::create([
            'name' => 'Empleado',
            'profile' => 'Employee',
            'status' => 'Active',
            'email' => 'empleado@gmail.com',
            'password' => bcrypt('flaminco2021')
        ]);



        //creación de roles
        $admin    = Role::create(['name' => 'Admin']);
        $empleado = Role::create(['name' => 'Employee']);
        $empleado = Role::create(['name' => 'Comercio']);



        //creación de permisos:
        //categories
        Permission::create(['name' => 'category_index']);
        Permission::create(['name' => 'category_create']);
        Permission::create(['name' => 'category_update']);
        Permission::create(['name' => 'category_destroy']);
        Permission::create(['name' => 'category_edit']);
        Permission::create(['name' => 'category_search']);
        //products
        Permission::create(['name' => 'product_index']);
        Permission::create(['name' => 'product_create']);
        Permission::create(['name' => 'product_update']);
        Permission::create(['name' => 'product_destroy']);
        Permission::create(['name' => 'product_edit']);
        Permission::create(['name' => 'product_search']);
        //users
        Permission::create(['name' => 'user_index']);
        Permission::create(['name' => 'user_create']);
        Permission::create(['name' => 'user_update']);
        Permission::create(['name' => 'user_destroy']);
        Permission::create(['name' => 'user_edit']);
        Permission::create(['name' => 'user_search']);
        //denominations
        Permission::create(['name' => 'denomination_index']);
        Permission::create(['name' => 'denomination_create']);
        Permission::create(['name' => 'denomination_update']);
        Permission::create(['name' => 'denomination_destroy']);
        Permission::create(['name' => 'denomination_edit']);
        Permission::create(['name' => 'denomination_search']);
        //sales
        Permission::create(['name' => 'sale_index']);
        Permission::create(['name' => 'sale_create']);

        //roles
        Permission::create(['name' => 'role_index']);
        Permission::create(['name' => 'role_create']);
        Permission::create(['name' => 'role_update']);
        Permission::create(['name' => 'role_destroy']);
        Permission::create(['name' => 'role_edit']);
        Permission::create(['name' => 'role_search']);
        //permissions
        Permission::create(['name' => 'permission_index']);
        Permission::create(['name' => 'permission_create']);
        Permission::create(['name' => 'permission_update']);
        Permission::create(['name' => 'permission_destroy']);
        Permission::create(['name' => 'permission_edit']);
        Permission::create(['name' => 'permission_search']);
        //assign
        Permission::create(['name' => 'assign_index']);
        Permission::create(['name' => 'assign_syncall']);
        Permission::create(['name' => 'assign_revokeall']);
        Permission::create(['name' => 'assign_checkbox']);
        //cash out
        Permission::create(['name' => 'cashout_index']);
        Permission::create(['name' => 'cashout_print']);
        Permission::create(['name' => 'cashout_detail']);
        //reports
        Permission::create(['name' => 'report_index']);
        Permission::create(['name' => 'report_pdf']);
        Permission::create(['name' => 'report_excel']);












        //asignar permisos al role Admin
        $admin->givePermissionTo([
            'category_index',
            'category_create',
            'category_update',
            'category_destroy',
            'category_edit',
            'category_search',
            'product_index',
            'product_create',
            'product_update',
            'product_destroy',
            'product_edit',
            'product_search',
            'user_index',
            'user_create',
            'user_update',
            'user_destroy',
            'user_edit',
            'user_search',
            'denomination_index',
            'denomination_create',
            'denomination_update',
            'denomination_destroy',
            'denomination_edit',
            'denomination_search',
            'role_index',
            'role_create',
            'role_update',
            'role_destroy',
            'role_edit',
            'role_search',
            'permission_index',
            'permission_create',
            'permission_update',
            'permission_destroy',
            'permission_edit',
            'permission_search',
            'sale_index',
            'sale_create',
            'assign_index',
            'assign_syncall',
            'assign_revokeall',
            'assign_checkbox',
            'cashout_index',
            'cashout_print',
            'cashout_detail',
            'report_index',
            'report_pdf',
            'report_excel'
        ]);

  

        //asignar permisos al role Employee
        $empleado->givePermissionTo([
            'category_index',
            'product_index',
            'user_index',
            'denomination_index',
            'role_index',
            'permission_index',
            'sale_index',
            'sale_create',
            'assign_index',
            'cashout_index',
            'report_index',
        ]);





        //asignar rol al usuario admin
        $uAdmin = User::find(1);
        $uAdmin->assignRole('Admin');

        //asignar rol al usuario empleado
        $uEmpleado = User::find(2);
        $uEmpleado->assignRole('Employee');

    }
}
