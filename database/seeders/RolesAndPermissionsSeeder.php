<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\{Permission, Role};

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'rh.view',
            'rh.manage',
            'employees.view',
            'employees.manage',
            'departments.manage',
            'positions.manage',
            'payroll.view',
            'payroll.generate',
            // financeiro.* (mais tarde)
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm);
        }

        $admin = Role::findOrCreate('admin');
        $rh = Role::findOrCreate('rh');
        $financeiro = Role::findOrCreate('financeiro');
        $consultor = Role::findOrCreate('consultor');
        $gerente = Role::findOrCreate('gerente');
        $funcionario = Role::findOrCreate('funcionario');

        $admin->givePermissionTo(Permission::all());

        $rh->givePermissionTo([
            'rh.view',
            'rh.manage',
            'employees.view',
            'employees.manage',
            'departments.manage',
            'positions.manage',
            'payroll.view',
            'payroll.generate',
        ]);

        $gerente->givePermissionTo([
            'rh.view',
            'employees.view',
            'payroll.view',
        ]);

        $consultor->givePermissionTo([
            'rh.view',
            'employees.view',
        ]);

        $funcionario->givePermissionTo([
            'rh.view',
            // futuramente: ver holerite próprio
        ]);

        // financeiro perms (quando iniciar módulo)
        $financeiro->givePermissionTo([
            // ...
        ]);
    }
}
