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
        // Limpa cache de permissões
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // =================
        // PERMISSÕES MODULARES
        // =================
        $permissions = [
            // Módulo RH Geral
            'rh.dashboard.view',

            // Funcionários
            'funcionarios.view',
            'funcionarios.create',
            'funcionarios.edit',
            'funcionarios.delete',

            // Departamentos
            'departamentos.view',
            'departamentos.manage',

            // Cargos
            'cargos.view',
            'cargos.manage',

            // Folha de Pagamento
            'folha.view',
            'folha.create',
            'folha.generate',
            'folha.close',
            'folha.reopen',
            'folha.reports',

            // Financeiro (para o futuro)
            'financeiro.dashboard.view',
            'boletos.view',
            'boletos.create',
            'boletos.pay',
            'cartoes.view',
            'cartoes.manage',
            'financeiro.reports',
        ];

        // Criar todas as permissões
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // =================
        // ROLES (PERFIS)
        // =================
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $rh = Role::firstOrCreate(['name' => 'rh']);
        $financeiro = Role::firstOrCreate(['name' => 'financeiro']);
        $gerente = Role::firstOrCreate(['name' => 'gerente']);
        $consultor = Role::firstOrCreate(['name' => 'consultor']);
        $funcionario = Role::firstOrCreate(['name' => 'funcionario']);

        // =================
        // DISTRIBUIÇÃO DE PERMISSÕES
        // =================

        // ADMIN: Todas as permissões
        $admin->syncPermissions(Permission::all());

        // RH: Módulo RH completo
        $rh->syncPermissions([
            'rh.dashboard.view',
            'funcionarios.view',
            'funcionarios.create',
            'funcionarios.edit',
            'funcionarios.delete',
            'departamentos.view',
            'departamentos.manage',
            'cargos.view',
            'cargos.manage',
            'folha.view',
            'folha.create',
            'folha.generate',
            'folha.close',
            'folha.reopen',
            'folha.reports',
        ]);

        // FINANCEIRO: Módulo Financeiro completo
        $financeiro->syncPermissions([
            'financeiro.dashboard.view',
            'boletos.view',
            'boletos.create',
            'boletos.pay',
            'cartoes.view',
            'cartoes.manage',
            'financeiro.reports',
        ]);

        // GERENTE: Visualização geral + relatórios
        $gerente->syncPermissions([
            'rh.dashboard.view',
            'funcionarios.view',
            'departamentos.view',
            'cargos.view',
            'folha.view',
            'folha.reports',
            'financeiro.dashboard.view',
            'boletos.view',
            'cartoes.view',
            'financeiro.reports',
        ]);

        // CONSULTOR: Apenas visualizações (sem editar)
        $consultor->syncPermissions([
            'rh.dashboard.view',
            'funcionarios.view',
            'departamentos.view',
            'cargos.view',
            'folha.view',
        ]);

        // FUNCIONÁRIO: Apenas ver próprios dados (implementaremos com Policy)
        $funcionario->syncPermissions([]);

        // =================
        // USUÁRIOS PADRÃO PARA TESTE
        // =================

        // Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@teste.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['admin']);

        // RH
        $rhUser = User::firstOrCreate(
            ['email' => 'rh@teste.com'],
            [
                'name' => 'RH Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $rhUser->syncRoles(['rh']);

        // Financeiro
        $financeiroUser = User::firstOrCreate(
            ['email' => 'financeiro@teste.com'],
            [
                'name' => 'Financeiro Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $financeiroUser->syncRoles(['financeiro']);

        // Gerente
        $gerenteUser = User::firstOrCreate(
            ['email' => 'gerente@teste.com'],
            [
                'name' => 'Gerente Geral',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $gerenteUser->syncRoles(['gerente']);

        echo "✅ Roles e permissões criadas com sucesso!\n";
        echo "📧 Usuários de teste:\n";
        echo "   - admin@teste.com (password)\n";
        echo "   - rh@teste.com (password)\n";
        echo "   - financeiro@teste.com (password)\n";
        echo "   - gerente@teste.com (password)\n";
    }
}
