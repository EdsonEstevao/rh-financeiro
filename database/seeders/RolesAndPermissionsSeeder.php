<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\{Permission, Role};

use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'rh.visualizar',
            'rh.gerenciar',
            'funcionarios.visualizar',
            'funcionarios.gerenciar',
            'departamentos.gerenciar',
            'cargos.gerenciar',
            'folha.visualizar',
            'folha.gerar',
        ];

        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm);
        }

        // Roles
        $admin = Role::findOrCreate('admin');
        $rh = Role::findOrCreate('rh');
        $financeiro = Role::findOrCreate('financeiro');
        $consultor = Role::findOrCreate('consultor');
        $gerente = Role::findOrCreate('gerente');
        $funcionario = Role::findOrCreate('funcionario');

        // Admin em todas as permissoes
        $admin->givePermissionTo(Permission::all());

        $rh->givePermissionTo([
            'rh.visualizar',
            'rh.gerenciar',
            'funcionarios.visualizar',
            'funcionarios.gerenciar',
            'departamentos.gerenciar',
            'cargos.gerenciar',
            'folha.visualizar',
            'folha.gerar',
        ]);

        // Gerente
        $gerente->givePermissionTo([
            'rh.visualizar',
            'funcionarios.visualizar',
            'folha.visualizar',
        ]);

        // Consultor
        $consultor->givePermissionTo([
            'rh.visualizar',
            'funcionarios.visualizar',
        ]);

        // Funcionario
        $funcionario->givePermissionTo([
            'rh.visualizar',
        ]);

        // Funcionario básico
        $funcionario->givePermissionTo([
            'rh.view',
            // futuramente: ver holerite próprio
        ]);



        // financeiro perms (quando iniciar módulo)
        $financeiro->givePermissionTo([
            // ...
        ]);



        // Criar usuário admin padrão
        $user = User::firstOrCreate(
            ['email' => 'admin@teste.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
