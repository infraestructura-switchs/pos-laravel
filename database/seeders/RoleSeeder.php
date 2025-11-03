<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder {

    public function run() {

        $permissions = Permission::all()->pluck('name');

        $role = Role::findOrCreate('Administrador', 'web');
        $role->syncPermissions($permissions);

        $role = Role::findOrCreate('Cajero', 'web');
        $role->syncPermissions(['facturas', 'financiaciones']);

    }

}
