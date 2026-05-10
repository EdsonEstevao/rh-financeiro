<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Domain\RH\{Cargo, Departamento, Funcionario};


class DadosTesteSeeder extends Seeder
{
    public function run(): void
    {
        // Departamentos
        $departamentos = [
            ['nome' => 'Recursos Humanos', 'ativo' => true],
            ['nome' => 'Financeiro', 'ativo' => true],
            ['nome' => 'Vendas', 'ativo' => true],
            ['nome' => 'Tecnologia da Informação', 'ativo' => true],
            ['nome' => 'Marketing', 'ativo' => true],
        ];

        foreach ($departamentos as $dept) {
            Departamento::firstOrCreate(['nome' => $dept['nome']], $dept);
        }

        // Cargos
        $cargos = [
            ['titulo' => 'Gerente de RH', 'ativo' => true],
            ['titulo' => 'Analista de RH', 'ativo' => true],
            ['titulo' => 'Contador', 'ativo' => true],
            ['titulo' => 'Auxiliar Contábil', 'ativo' => true],
            ['titulo' => 'Vendedor', 'ativo' => true],
            ['titulo' => 'Supervisor de Vendas', 'ativo' => true],
            ['titulo' => 'Desenvolvedor PHP', 'ativo' => true],
            ['titulo' => 'Analista de Sistemas', 'ativo' => true],
            ['titulo' => 'Designer Gráfico', 'ativo' => true],
        ];

        foreach ($cargos as $cargo) {
            Cargo::firstOrCreate(['titulo' => $cargo['titulo']], $cargo);
        }

        // Funcionários de exemplo
        $funcionarios = [
            [
                'nome_completo' => 'João Silva Santos',
                'cpf' => '123.456.789-01',
                'rg' => '12.345.678-9',
                'data_nascimento' => '1985-05-15',
                'estado_civil' => 'casado',
                'genero' => 'masculino',
                'telefone' => '(11) 3456-7890',
                'celular' => '(11) 99999-1234',
                'email' => 'joao.silva@empresa.com',
                'cep' => '01234-567',
                'logradouro' => 'Rua das Flores, 123',
                'numero' => '123',
                'bairro' => 'Centro',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'data_admissao' => '2020-03-01',
                'tipo_contrato' => 'clt',
                'salario_base' => 5000.00,
                'ctps_numero' => '12345678901',
                'ctps_serie' => '001',
                'ctps_uf' => 'SP',
                'pis_pasep' => '123.45678.90-1',
                'banco_codigo' => '001',
                'banco_nome' => 'Banco do Brasil',
                'agencia' => '1234',
                'conta' => '56789-0',
                'tipo_conta' => 'corrente',
                'departamento_id' => 1, // RH
                'cargo_id' => 1, // Gerente de RH
            ],
            [
                'nome_completo' => 'Maria Oliveira Costa',
                'cpf' => '987.654.321-09',
                'rg' => '98.765.432-1',
                'data_nascimento' => '1990-08-22',
                'estado_civil' => 'solteiro',
                'genero' => 'feminino',
                'telefone' => '(11) 2345-6789',
                'celular' => '(11) 88888-5678',
                'email' => 'maria.oliveira@empresa.com',
                'cep' => '04567-890',
                'logradouro' => 'Avenida Paulista, 1000',
                'numero' => '1000',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'data_admissao' => '2021-07-15',
                'tipo_contrato' => 'clt',
                'salario_base' => 3500.00,
                'ctps_numero' => '98765432109',
                'ctps_serie' => '002',
                'ctps_uf' => 'SP',
                'pis_pasep' => '987.65432.10-9',
                'banco_codigo' => '341',
                'banco_nome' => 'Itaú',
                'agencia' => '5678',
                'conta' => '12345-6',
                'tipo_conta' => 'corrente',
                'departamento_id' => 2, // Financeiro
                'cargo_id' => 3, // Contador
            ],
        ];

        foreach ($funcionarios as $func) {
            Funcionario::firstOrCreate(['cpf' => $func['cpf']], $func);
        }
    }
}