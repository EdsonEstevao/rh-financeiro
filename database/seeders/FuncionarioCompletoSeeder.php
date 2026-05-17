<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Domain\RH\{Cargo, Departamento, Dependente, Funcionario};

class FuncionarioCompletoSeeder extends Seeder
{
    private array $departamentos = [];
    private array $cargos = [];

    public function run(): void
    {
        // Excluir dados anteriores
        $this->command->info('🗑️ Excluindo dados anteriores...');
        // Primeiro dados dependentes
        Dependente::query()->delete();
        // Depois dados funcionários
        Funcionario::query()->delete();





        $this->command->info('🏢 Criando estrutura organizacional...');
        $this->criarDepartamentosECargos();

        $this->command->info('👥 Gerando funcionários com dados completos...');

        $funcionarios = $this->getFuncionariosData();
        $totalCriados = 0;

        foreach ($funcionarios as $dados) {
            DB::transaction(function () use ($dados, &$totalCriados) {
                $funcionario = $this->criarFuncionarioCompleto($dados);

                // $this->command->line(
                //     "  ✅ <fg=cyan>{$funcionario->nome_completo}</> | " .
                //     "<fg=yellow>{$funcionario->cargo->titulo}</> | " .
                //     "Admissão: <fg=green>{$funcionario->data_admissao->format('d/m/Y')}</> | " .
                //     "Férias: <fg=blue>{$funcionario->ferias_vencimento->format('d/m/Y')}</>"
                // );

                // $this->command->line(
                //     "  ✅ <fg=cyan>{$funcionario->nome_completo}</> | " .
                //     "<fg=yellow>" . ($funcionario->cargo->titulo ?? 'Sem cargo') . "</> | " .
                //     "Admissão: <fg=green>" . ($funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'N/A') . "</> | " .
                //     "Férias: <fg=blue>" . ($funcionario->ferias_vencimento ? $funcionario->ferias_vencimento->format('d/m/Y') : 'Não se aplica') . "</>"
                // );

                // $this->command->line(
                //     "  ✅ <fg=cyan>{$funcionario->nome_completo}</> | " .
                //     "<fg=yellow>" . ($funcionario->cargo->titulo ?? 'Sem cargo') . "</> | " .
                //     "Admissão: <fg=green>" . ($funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'N/A') . "</> | " .
                //     "Férias: <fg=blue>" . ($funcionario->ferias_vencimento ? $funcionario->ferias_vencimento->format('d/m/Y') : 'N/A (PJ)') . "</>"
                // );

                // Busca o período de férias previstas
                $feriasPrevistas = $funcionario->periodoFerias()
                    ->where('tipo', 'prevista')
                    ->where('status', 'planejada')
                    ->first();

                $inicioFerias = $feriasPrevistas?->data_inicio;
                $fimFerias = $feriasPrevistas?->data_fim;
                $vencimento = $funcionario->ferias_vencimento;

                $this->command->line(
                    "  ✅ <fg=cyan>{$funcionario->nome_completo}</> | " .
                    "<fg=yellow>" . ($funcionario->cargo->titulo ?? 'Sem cargo') . "</> | " .
                    "Admissão: <fg=green>" . ($funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'N/A') . "</> | " .
                    "Férias: <fg=blue>" . ($inicioFerias ? $inicioFerias->format('d/m/Y') . ' a ' . $fimFerias->format('d/m/Y') : 'N/A (PJ)') . "</> | " .
                    "Vence: <fg=yellow>" . ($vencimento ? $vencimento->format('d/m/Y') : '-') . "</>"
                );

                $totalCriados++;
            });
        }

        $this->command->newLine();
        $this->command->info("🎉 Total de funcionários criados: {$totalCriados}");
        $this->command->info("📅 Todas as férias previstas foram geradas automaticamente!");
    }

    /**
     * Criar estrutura base (departamentos e cargos)
     */
    private function criarDepartamentosECargos(): void
    {
        // Departamentos
        $deptos = [
            ['nome' => 'Administrativo'],
            ['nome' => 'Financeiro'],
            ['nome' => 'Recursos Humanos'],
            ['nome' => 'Tecnologia da Informação'],
            ['nome' => 'Operações'],
            ['nome' => 'Comercial'],
            ['nome' => 'Jurídico'],
            ['nome' => 'Marketing'],
        ];

        foreach ($deptos as $depto) {
            $this->departamentos[$depto['nome']] = Departamento::firstOrCreate(
                ['nome' => $depto['nome']],
                ['ativo' => true]
            );
        }

        // Cargos
        $cargosList = [
            ['titulo' => 'Assistente Administrativo'],
            ['titulo' => 'Analista Financeiro'],
            ['titulo' => 'Analista de RH'],
            ['titulo' => 'Desenvolvedor'],
            ['titulo' => 'Gerente de Projetos'],
            ['titulo' => 'Vendedor'],
            ['titulo' => 'Advogado'],
            ['titulo' => 'Analista de Marketing'],
            ['titulo' => 'Coordenador'],
            ['titulo' => 'Diretor'],
            ['titulo' => 'Estagiário'],
            ['titulo' => 'Aprendiz'],
        ];

        foreach ($cargosList as $cargo) {
            $this->cargos[$cargo['titulo']] = Cargo::firstOrCreate(
                ['titulo' => $cargo['titulo']],
                ['ativo' => true]
            );
        }
    }

    /**
     * Criar um funcionário com todos os relacionamentos
     */
    private function criarFuncionarioCompleto(array $dados): Funcionario
    {
        // 1. Criar funcionário (dados pessoais básicos)
        $funcionario = Funcionario::create([
            'departamento_id' => $dados['departamento_id'],
            'cargo_id' => $dados['cargo_id'],
            'nome_completo' => $dados['nome_completo'],
            'data_nascimento' => $dados['data_nascimento'],
            'estado_civil' => $dados['estado_civil'],
            'genero' => $dados['genero'],
            'nacionalidade' => 'brasileira',
            'naturalidade' => $dados['naturalidade'] ?? null,
            'ativo' => $dados['ativo'] ?? true,
            'observacoes' => $dados['observacoes'] ?? null,
        ]);

        // 2. Criar endereço
        $funcionario->endereco()->create([
            'cep' => $dados['cep'],
            'logradouro' => $dados['logradouro'],
            'numero' => $dados['numero'],
            'complemento' => $dados['complemento'] ?? null,
            'bairro' => $dados['bairro'],
            'cidade' => $dados['cidade'],
            'estado' => $dados['estado'],
        ]);

        // 3. Criar documentos
        $funcionario->documentos()->create([
            'cpf' => $dados['cpf'],
            'rg' => $dados['rg'],
            'orgao_expedidor_rg' => $dados['orgao_expedidor_rg'] ?? 'SSP',
            'ctps_numero' => $dados['ctps_numero'] ?? $this->gerarCTPS(),
            'ctps_serie' => $dados['ctps_serie'] ?? '001',
            'ctps_uf' => $dados['ctps_uf'] ?? $dados['estado'],
            'ctps_data_emissao' => $dados['ctps_data_emissao'] ?? Carbon::parse($dados['data_admissao'])->subYears(2),
            'pis_pasep' => $dados['pis_pasep'] ?? $this->gerarPIS(),
        ]);

        // 4. Criar contatos
        $funcionario->contatos()->create([
            'telefone' => $dados['telefone'] ?? $this->gerarTelefone(),
            'celular' => $dados['celular'],
            'email' => $dados['email'],
            'email_pessoal' => $dados['email_pessoal'] ?? null,
        ]);

        // 5. Criar dados bancários
        $bancos = [
            ['codigo' => '001', 'nome' => 'Banco do Brasil'],
            ['codigo' => '341', 'nome' => 'Itaú'],
            ['codigo' => '237', 'nome' => 'Bradesco'],
            ['codigo' => '104', 'nome' => 'Caixa Econômica'],
            ['codigo' => '033', 'nome' => 'Santander'],
        ];
        $banco = $bancos[array_rand($bancos)];

        $funcionario->dadosBancarios()->create([
            'banco_codigo' => $banco['codigo'],
            'banco_nome' => $banco['nome'],
            'agencia' => str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'conta' => str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) . '-' . rand(0, 9),
            'tipo_conta' => ['corrente', 'poupanca'][array_rand(['corrente', 'poupanca'])],
        ]);

        // 6. Criar contrato (ISSO DISPARA FÉRIAS AUTOMÁTICAS!)
        $funcionario->contrato()->create([
            'data_admissao' => $dados['data_admissao'],
            'data_demissao' => $dados['data_demissao'] ?? null,
            'tipo_contratacao' => $dados['tipo_contratacao'] ?? 'clt',
            'tipo_contrato' => $dados['tipo_contrato'] ?? 'indeterminado',
            'tipo_remuneracao' => 'mensal',
            'salario_base' => $dados['salario_base'],
            'carga_horaria_semanal' => $dados['carga_horaria_semanal'] ?? 44,
            'horario_entrada' => '08:00:00',
            'horario_saida' => '17:00:00',
            'horario_almoco_inicio' => '12:00:00',
            'horario_almoco_fim' => '13:00:00',
            'local_trabalho' => $dados['local_trabalho'] ?? $dados['cidade'],
            'qtd_dependentes_ir' => $dados['qtd_dependentes_ir'] ?? 0,
            'qtd_dependentes_salario_familia' => $dados['qtd_dependentes_salario_familia'] ?? 0,
        ]);

        // 7. Criar benefícios
        $funcionario->beneficios()->create([
            'vale_transporte' => $dados['vale_transporte'] ?? false,
            'valor_vale_transporte' => $dados['valor_vale_transporte'] ?? null,
            'vale_alimentacao' => $dados['vale_alimentacao'] ?? false,
            'valor_vale_alimentacao' => $dados['valor_vale_alimentacao'] ?? null,
            'plano_saude' => $dados['plano_saude'] ?? false,
            'plano_odontologico' => $dados['plano_odontologico'] ?? false,
        ]);

        // 8. Criar dependentes (se tiver)
        if (!empty($dados['dependentes'])) {
            foreach ($dados['dependentes'] as $dependente) {
                $funcionario->dependentes()->create($dependente);
            }
        }

        // ✅ RECARREGA O MODEL PARA TER AS FÉRIAS ATUALIZADAS
        $funcionario->refresh();

        return $funcionario->load([
            'endereco', 'documentos', 'contatos',
            'dadosBancarios', 'contrato', 'beneficios',
            'dependentes', 'cargo', 'departamento',
            'periodoFerias' // ✅ Férias já foram geradas automaticamente!
        ]);
    }

    /**
     * Dados dos funcionários
     */
    private function getFuncionariosData(): array
    {
        return [
            // ─── FUNCIONÁRIO 1: CLT Padrão ─────────────────────
            [
                'departamento_id' => $this->departamentos['Tecnologia da Informação']->id,
                'cargo_id' => $this->cargos['Desenvolvedor']->id,
                'nome_completo' => 'João Silva Santos',
                'data_nascimento' => '1985-05-15',
                'estado_civil' => 'casado',
                'genero' => 'masculino',
                'naturalidade' => 'São Paulo',
                'cep' => '01234-567',
                'logradouro' => 'Rua das Flores',
                'numero' => '123',
                'bairro' => 'Centro',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cpf' => '123.456.789-01',
                'rg' => '12.345.678-9',
                'celular' => '(11) 99999-1234',
                'email' => 'joao.silva@empresa.com',
                'data_admissao' => '2025-05-13', // ✅ Vai gerar férias para 2026-05-13
                'tipo_contratacao' => 'clt',
                'tipo_contrato' => 'indeterminado',
                'salario_base' => 3500.00,
                'carga_horaria_semanal' => 44,
                'local_trabalho' => 'Matriz - São Paulo',
                'qtd_dependentes_salario_familia' => 2,
                'vale_transporte' => true,
                'valor_vale_transporte' => 220.00,
                'vale_alimentacao' => true,
                'valor_vale_alimentacao' => 450.00,
                'dependentes' => [
                    [
                        'nome_completo' => 'Pedro Silva',
                        'data_nascimento' => '2018-03-10',
                        'parentesco' => 'filho',
                        'invalido' => false,
                        'ativo' => true,
                    ],
                    [
                        'nome_completo' => 'Ana Silva',
                        'data_nascimento' => '2020-07-22',
                        'parentesco' => 'filha',
                        'invalido' => false,
                        'ativo' => true,
                    ],
                ],
            ],

            // ─── FUNCIONÁRIO 2: CLT Experiência ─────────────────
            [
                'departamento_id' => $this->departamentos['Administrativo']->id,
                'cargo_id' => $this->cargos['Assistente Administrativo']->id,
                'nome_completo' => 'Maria Oliveira Costa',
                'data_nascimento' => '1990-08-22',
                'estado_civil' => 'solteiro',
                'genero' => 'feminino',
                'naturalidade' => 'Rio de Janeiro',
                'cep' => '04567-890',
                'logradouro' => 'Avenida Paulista',
                'numero' => '1000',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cpf' => '987.654.321-09',
                'rg' => '98.765.432-1',
                'celular' => '(11) 88888-5678',
                'email' => 'maria.oliveira@empresa.com',
                'data_admissao' => '2025-07-15', // ✅ Férias previstas para 2026-07-15
                'tipo_contratacao' => 'clt',
                'tipo_contrato' => 'experiencia',
                'salario_base' => 1800.00,
                'carga_horaria_semanal' => 44,
                'local_trabalho' => 'Filial - Campinas',
                'qtd_dependentes_salario_familia' => 0,
                'dependentes' => [],
            ],

            // ─── FUNCIONÁRIO 3: Gerente CLT ────────────────────
            [
                'departamento_id' => $this->departamentos['Comercial']->id,
                'cargo_id' => $this->cargos['Gerente de Projetos']->id,
                'nome_completo' => 'Carlos Eduardo Pereira',
                'data_nascimento' => '1980-12-03',
                'estado_civil' => 'casado',
                'genero' => 'masculino',
                'naturalidade' => 'Belo Horizonte',
                'cep' => '30123-456',
                'logradouro' => 'Rua dos Gerânios',
                'numero' => '500',
                'bairro' => 'Funcionários',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'cpf' => '456.789.123-45',
                'rg' => '45.678.912-3',
                'celular' => '(31) 97777-8901',
                'email' => 'carlos.pereira@empresa.com',
                'data_admissao' => '2024-01-10', // ✅ Já tem direito a férias! (>12 meses)
                'tipo_contratacao' => 'clt',
                'tipo_contrato' => 'indeterminado',
                'salario_base' => 7200.00,
                'carga_horaria_semanal' => 44,
                'local_trabalho' => 'Matriz - Belo Horizonte',
                'qtd_dependentes_salario_familia' => 1,
                'plano_saude' => true,
                'plano_odontologico' => true,
                'dependentes' => [
                    [
                        'nome_completo' => 'Mariana Pereira',
                        'data_nascimento' => '2015-11-30',
                        'parentesco' => 'filha',
                        'invalido' => false,
                        'ativo' => true,
                    ],
                ],
            ],

            // ─── FUNCIONÁRIO 4: Aprendiz ───────────────────────
            [
                'departamento_id' => $this->departamentos['Administrativo']->id,
                'cargo_id' => $this->cargos['Aprendiz']->id,
                'nome_completo' => 'Ana Júlia Martins',
                'data_nascimento' => '2006-03-18',
                'estado_civil' => 'solteiro',
                'genero' => 'feminino',
                'naturalidade' => 'Curitiba',
                'cep' => '80012-345',
                'logradouro' => 'Rua XV de Novembro',
                'numero' => '200',
                'bairro' => 'Centro',
                'cidade' => 'Curitiba',
                'estado' => 'PR',
                'cpf' => '789.123.456-78',
                'rg' => '78.912.345-6',
                'celular' => '(41) 96666-2345',
                'email' => 'ana.martins@empresa.com',
                'data_admissao' => '2026-01-05', // ✅ Férias previstas para 2027-01-05
                'tipo_contratacao' => 'clt',
                'tipo_contrato' => 'aprendiz',
                'salario_base' => 750.00,
                'carga_horaria_semanal' => 30,
                'local_trabalho' => 'Matriz - Curitiba',
                'qtd_dependentes_salario_familia' => 0,
                'dependentes' => [],
            ],

            // ─── FUNCIONÁRIO 5: Estagiário ─────────────────────
            [
                'departamento_id' => $this->departamentos['Tecnologia da Informação']->id,
                'cargo_id' => $this->cargos['Estagiário']->id,
                'nome_completo' => 'Lucas Henrique Alves',
                'data_nascimento' => '2002-09-10',
                'estado_civil' => 'solteiro',
                'genero' => 'masculino',
                'naturalidade' => 'Florianópolis',
                'cep' => '88015-678',
                'logradouro' => 'Rua das Palmeiras',
                'numero' => '50',
                'bairro' => 'Centro',
                'cidade' => 'Florianópolis',
                'estado' => 'SC',
                'cpf' => '321.654.987-10',
                'rg' => '32.165.498-7',
                'celular' => '(48) 95555-6789',
                'email' => 'lucas.alves@empresa.com',
                'data_admissao' => '2026-03-01', // ✅ Férias previstas para 2027-03-01
                'tipo_contratacao' => 'clt',
                'tipo_contrato' => 'estagio',
                'salario_base' => 1200.00,
                'carga_horaria_semanal' => 30,
                'local_trabalho' => 'Filial - Florianópolis',
                'qtd_dependentes_salario_familia' => 0,
                'dependentes' => [],
            ],

            // ─── FUNCIONÁRIO 6: PJ (sem férias) ────────────────
            [
                'departamento_id' => $this->departamentos['Marketing']->id,
                'cargo_id' => $this->cargos['Analista de Marketing']->id,
                'nome_completo' => 'Fernanda Souza Lima',
                'data_nascimento' => '1988-06-25',
                'estado_civil' => 'divorciado',
                'genero' => 'feminino',
                'naturalidade' => 'Salvador',
                'cep' => '40012-345',
                'logradouro' => 'Avenida Oceânica',
                'numero' => '300',
                'bairro' => 'Barra',
                'cidade' => 'Salvador',
                'estado' => 'BA',
                'cpf' => '654.321.987-00',
                'rg' => '65.432.198-7',
                'celular' => '(71) 94444-3456',
                'email' => 'fernanda.lima@empresa.com',
                'data_admissao' => '2025-09-01',
                'tipo_contratacao' => 'pj',
                'tipo_contrato' => 'determinado',
                'salario_base' => 5500.00,
                'carga_horaria_semanal' => 40,
                'local_trabalho' => 'Home Office',
                'qtd_dependentes_salario_familia' => 0,
                'plano_saude' => false,
                'dependentes' => [],
            ],

            // ─── FUNCIONÁRIO 7: Temporário ─────────────────────
            [
                'departamento_id' => $this->departamentos['Operações']->id,
                'cargo_id' => $this->cargos['Coordenador']->id,
                'nome_completo' => 'Rafael Gonçalves',
                'data_nascimento' => '1992-04-12',
                'estado_civil' => 'casado',
                'genero' => 'masculino',
                'naturalidade' => 'Porto Alegre',
                'cep' => '90012-345',
                'logradouro' => 'Rua da Praia',
                'numero' => '800',
                'bairro' => 'Centro Histórico',
                'cidade' => 'Porto Alegre',
                'estado' => 'RS',
                'cpf' => '111.222.333-44',
                'rg' => '11.122.233-3',
                'celular' => '(51) 93333-7890',
                'email' => 'rafael.goncalves@empresa.com',
                'data_admissao' => '2026-02-15', // ✅ Férias previstas para 2027-02-15
                'tipo_contratacao' => 'clt',
                'tipo_contrato' => 'temporario',
                'salario_base' => 2800.00,
                'carga_horaria_semanal' => 44,
                'local_trabalho' => 'Filial - Porto Alegre',
                'qtd_dependentes_salario_familia' => 3,
                'dependentes' => [
                    [
                        'nome_completo' => 'Gabriel Gonçalves',
                        'data_nascimento' => '2016-02-20',
                        'parentesco' => 'filho',
                        'invalido' => false,
                        'ativo' => true,
                    ],
                    [
                        'nome_completo' => 'Isabella Gonçalves',
                        'data_nascimento' => '2018-09-15',
                        'parentesco' => 'filha',
                        'invalido' => false,
                        'ativo' => true,
                    ],
                    [
                        'nome_completo' => 'Miguel Gonçalves',
                        'data_nascimento' => '2021-12-01',
                        'parentesco' => 'filho',
                        'invalido' => false,
                        'ativo' => true,
                    ],
                ],
            ],
        ];
    }

    // ─── HELPERS ─────────────────────────────────

    private function gerarCPF(): string
    {
        return sprintf(
            '%03d.%03d.%03d-%02d',
            rand(100, 999), rand(100, 999), rand(100, 999), rand(0, 99)
        );
    }

    private function gerarCTPS(): string
    {
        return str_pad(rand(10000000, 99999999), 10, '0', STR_PAD_LEFT);
    }

    private function gerarPIS(): string
    {
        return sprintf(
            '%03d.%05d.%02d-%d',
            rand(100, 999), rand(10000, 99999), rand(10, 99), rand(0, 9)
        );
    }

    private function gerarTelefone(): string
    {
        $ddd = rand(11, 99);
        return sprintf('(%02d) %04d-%04d', $ddd, rand(2000, 9999), rand(1000, 9999));
    }
}