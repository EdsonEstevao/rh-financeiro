<?php

namespace App\Services\RH;

use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Carbon;

use App\Models\Domain\RH\Funcionario;

class FuncionarioService
{
    public function __construct(protected PeriodoFeriasService $periodoFeriasService) {}
    /**
     * Criar funcionário com validações e cálculo automático de férias
     */
    // public function criarFuncionario(array $dados): Funcionario
    // {
    //     return DB::transaction(function () use ($dados) {

    //         dd('Service',$dados);
    //         // Validações adicionais de negócio
    //         $this->validarIdadeMinima($dados['data_nascimento']);
    //         $this->validarCPF($dados['cpf']);

    //         // Criar funcionário (o boot do model já calcula férias automaticamente)
    //         $funcionario = Funcionario::create($dados);

    //         // Log da operação
    //         Log::info('Funcionário cadastrado', [
    //             'funcionario_id' => $funcionario->id,
    //             'nome' => $funcionario->nome_completo,
    //             'data_admissao' => $funcionario->data_admissao,
    //             'ferias_vencimento' => $funcionario->ferias_vencimento
    //         ]);

    //         /**
    //          * Cria funcionário e já registra um período de férias prevista (planejada)
    //          * 12 meses após a admissão, por padrão 30 dias corridos.
    //          */
    //         $this->criarFeriasPrevistaAoAdmitir($funcionario);

    //         return $funcionario;
    //     });
    // }

    /**
     * Criar funcionário completo (com todas as tabelas relacionadas)
     */
    public function criarFuncionario(array $dados): Funcionario
    {
        return DB::transaction(function () use ($dados) {
            // 1. Criar funcionário (dados básicos)
            $funcionario = Funcionario::create([
                'user_id' => $dados['user_id'] ?? null,
                'departamento_id' => $dados['departamento_id'],
                'cargo_id' => $dados['cargo_id'],
                'nome_completo' => $dados['nome_completo'],
                'data_nascimento' => $dados['data_nascimento'],
                'estado_civil' => $dados['estado_civil'],
                'genero' => $dados['genero'],
                'nacionalidade' => $dados['nacionalidade'] ?? 'brasileira',
                'naturalidade' => $dados['naturalidade'] ?? null,
                'ativo' => true,
            ]);

            // 2. Criar endereço
            $funcionario->endereco()->create($dados);

            // 3. Criar documentos
            $funcionario->documentos()->create($dados);

            // 4. Criar contatos
            $funcionario->contatos()->create($dados);

            // 5. Criar dados bancários
            $funcionario->dadosBancarios()->create($dados);

            // 6. Criar contrato (isso dispara automaticamente férias previstas)
            $funcionario->contrato()->create($dados);

            // 7. Criar benefícios
            $funcionario->beneficios()->create($dados);

            // $this->criarFeriasPrevistaAoAdmitir($funcionario);
             // ✅ RECARREGA O FUNCIONÁRIO COM TODOS OS DADOS ATUALIZADOS
            $funcionario->refresh();

            $funcionario->load([
                'endereco', 'documentos', 'contatos',
                'dadosBancarios', 'contrato', 'beneficios'
            ]);

            // 8. Criar dependentes
            if (!empty($dados['dependentes'])) {
                foreach ($dados['dependentes'] as $dependente) {
                    $funcionario->dependentes()->create($dependente);
                }
            }

            return $funcionario;
        });
    }

    private function criarFeriasPrevistaAoAdmitir(Funcionario $funcionario): void
    {

        $admissao = Carbon::parse($funcionario->data_admissao)->startOfDay();

        // 12 meses depois, preservando o "dia" quando possível
        // addYearNoOverflow evita cair em mês inválido (ex.: 31/03 -> 31/03 ok; 31/01 -> 31/01 ok; 31/08 -> 31/08 ok)
        // mas para fevereiro, ele ajusta para o último dia do mês.
        $inicioPrevisto = $admissao->copy()->addYearNoOverflow();

        // Padrão: 30 dias corridos (ajuste se sua regra for 30 "dias de férias" e não corridos)
        $fimPrevisto = $inicioPrevisto->copy()->addDays(29);

        /**
         * Vencimento do período concessivo: 24 meses após admissão (CLT Art. 134)
         * Se não gozar até esta data → férias em dobro (CLT Art. 137)
         */
        $vencimentoConcessivo = $admissao->copy()->addMonths(24)->toDateString();

        // Evitar duplicar caso o fluxo rode novamente
        $jaExistePrevista = $funcionario->periodoFerias()
            ->where('status', 'planejada')
            ->whereDate('data_inicio', $inicioPrevisto->toDateString())
            ->whereDate('data_fim', $fimPrevisto->toDateString())
            ->exists();

        // ✅ Melhor: verificar por número_periodo + ano de referência
        // $jaExistePrevista = $funcionario->periodoFerias()
        //     ->where('numero_periodo', 1)
        //     ->whereYear('data_inicio', $inicioPrevisto->year)
        //     ->exists();

        if ($jaExistePrevista) {
            return;
        }

        $this->periodoFeriasService->criarPeriodo($funcionario, [
            'data_inicio' => $inicioPrevisto->toDateString(),
            'data_fim'    => $fimPrevisto->toDateString(),
            'status'      => 'planejada',
            'observacao'  => 'Gerado automaticamente na admissão (férias previstas).',
            'numero_periodo' => 1,
            'abono_pecuniario' => false,

        ]);

        $funcionario->update(['ferias_vencimento' => $vencimentoConcessivo]);
    }

    /**
     * Atualiza funcionário e recalcula férias prevista se a data de admissão mudou
     */
    // public function atualizar(Funcionario $funcionario, array $dados): Funcionario
    // {
    //     return DB::transaction(function () use ($funcionario, $dados) {
    //         $dataAdmissaoAnterior = $funcionario->data_admissao;

    //         $funcionario->update($dados);

    //         // Se mudou a data de admissão, recalcula as férias previstas
    //         if (isset($dados['data_admissao']) && $dados['data_admissao'] != $dataAdmissaoAnterior) {
    //             $this->criarOuAtualizarFeriasPrevista($funcionario->fresh());
    //         }

    //         return $funcionario->fresh();
    //     });
    // }

    /**
     * Atualiza funcionário e recalcula férias previstas se a data de admissão mudou.
     */
    public function atualizar(Funcionario $funcionario, array $dados): Funcionario
    {
        return DB::transaction(function () use ($funcionario, $dados) {

            // Normaliza a data anterior para comparação segura
            $dataAdmissaoAnterior = $funcionario->data_admissao
                ? Carbon::parse($funcionario->data_admissao)->toDateString()
                : null;

            $funcionario->update($dados);

            // Verifica se a data de admissão foi enviada e realmente mudou
            if (isset($dados['data_admissao'])) {

                $dataAdmissaoNova = Carbon::parse($dados['data_admissao'])->toDateString();

                $admissaoMudou = $dataAdmissaoNova !== $dataAdmissaoAnterior;

                if ($admissaoMudou) {
                    // Recarrega o model uma única vez
                    $funcionarioAtualizado = $funcionario->fresh();

                     activity('rh')
                        ->performedOn($funcionarioAtualizado)
                        ->withProperties([
                            'data_admissao_anterior' => $dataAdmissaoAnterior,
                            'data_admissao_nova'     => $dataAdmissaoNova,
                        ])
                        ->log('Data de admissão alterada — férias previstas recalculadas.');

                    // Só recalcula se não há férias efetivadas
                    // (aprovadas ou já gozadas não devem ser recalculadas)
                    $temFeriasEfetivadas = $funcionarioAtualizado
                        ->periodoFerias()
                        ->whereIn('status', ['aprovada', 'gozada', 'em_gozo'])
                        ->exists();

                    if (!$temFeriasEfetivadas) {
                        $this->criarOuAtualizarFeriasPrevista($funcionarioAtualizado);
                    }
                }
            }

            // Retorna o model atualizado (já carregado acima ou recarregado aqui)
            return $funcionario->fresh();
        });
    }

    /**
     * Cria ou atualiza a férias prevista baseada na data de admissão
     * Regra: 12 meses após admissão, sempre 30 dias corridos
     */
    // private function criarOuAtualizarFeriasPrevista(Funcionario $funcionario): void
    // {
    //     $admissao = Carbon::parse($funcionario->data_admissao)->startOfDay();

    //     // 12 meses depois, preservando o "dia" quando possível
    //     $inicioPrevisto = $admissao->copy()->addYearNoOverflow();

    //     // Sempre 30 dias corridos (inicio + 29)
    //     $fimPrevisto = $inicioPrevisto->copy()->addDays(29);

    //     // Busca se já existe uma férias prevista para este funcionário
    //     $feriasPrevista = $funcionario->periodoFerias()
    //         ->where('tipo', 'prevista')
    //         ->first();

    //     $dadosPeriodo = [
    //         'data_inicio' => $inicioPrevisto->toDateString(),
    //         'data_fim'    => $fimPrevisto->toDateString(),
    //         'tipo'        => 'prevista',
    //         'status'      => 'planejada',
    //         'observacao'  => 'Gerado automaticamente baseado na data de admissão.',
    //         'numero_periodo' => 1,
    //         'abono_pecuniario' => false,
    //     ];

    //     if ($feriasPrevista) {
    //         // ATUALIZA a existente
    //         $this->periodoFeriasService->atualizarPeriodo($feriasPrevista, $dadosPeriodo);
    //     } else {
    //         // CRIA nova
    //         $this->periodoFeriasService->criarPeriodo($funcionario, $dadosPeriodo);
    //     }
    // }
    private function criarOuAtualizarFeriasPrevista(Funcionario $funcionario): void
    {
        $admissao = Carbon::parse($funcionario->data_admissao)->startOfDay();

        // CLT Art. 130 — direito adquirido após 12 meses
        $inicioPrevisto = $admissao->copy()->addYearNoOverflow();

        // CLT Art. 130 — 30 dias corridos (dia inicial = 1º dia)
        $fimPrevisto = $inicioPrevisto->copy()->addDays(29);

        // CLT Art. 134 — vencimento do período concessivo (24 meses da admissão)
        $vencimento = $admissao->copy()->addMonths(24)->toDateString();

        // Busca apenas período prevista/planejada (ainda não efetivado)
        // Ordenado por data para pegar o mais recente do 1º ciclo
        $feriasPrevista = $funcionario->periodoFerias()
            ->where('tipo', 'prevista')
            ->whereIn('status', ['planejada']) // ← só atualiza se ainda não efetivado
            ->where('numero_periodo', 1)
            ->orderBy('data_inicio')
            ->first();

        $dadosPeriodo = [
            'data_inicio'       => $inicioPrevisto->toDateString(),
            'data_fim'          => $fimPrevisto->toDateString(),
            'tipo'              => 'prevista',
            'status'            => 'planejada',
            'observacao'        => 'Gerado automaticamente baseado na data de admissão.',
            'numero_periodo'    => 1,
            'abono_pecuniario'  => false,

        ];

        // 'ferias_vencimento' => $vencimento, // ✅ prazo legal CLT Art. 134
        $funcionario->ferias_vencimento = $vencimento;
        $funcionario->save();

        if ($feriasPrevista) {
            // ✅ Só atualiza se ainda estiver como 'planejada'
            // (status aprovada/gozada/em_gozo são protegidos)
            $this->periodoFeriasService->atualizarPeriodo($feriasPrevista, $dadosPeriodo);
        } else {
            // Verifica se já existe período efetivado para o ciclo 1
            // (nesse caso não cria novo — o RH já tratou manualmente)
            $jaEfetivado = $funcionario->periodoFerias()
                ->where('numero_periodo', 1)
                ->whereIn('status', ['aprovada', 'gozada', 'em_gozo'])
                ->exists();

            if (!$jaEfetivado) {
                $this->periodoFeriasService->criarPeriodo($funcionario, $dadosPeriodo);
            }
        }
    }

    /**
     * Atualizar funcionário
     */
    // public function atualizarFuncionario(Funcionario $funcionario, array $dados): Funcionario
    // {

    //     // dd('Service', $dados);

    //     return DB::transaction(function () use ($funcionario, $dados) {
    //         $funcionario->update($dados);

    //         // Se mudou data de admissão, recalcular férias foi feito automaticamente no boot
    //         if (array_key_exists('data_admissao', $dados)) {
    //             Log::info('Período de férias recalculado', [
    //                 'funcionario_id' => $funcionario->id,
    //                 'nova_data_admissao' => $dados['data_admissao'],
    //                 'novo_vencimento_ferias' => $funcionario->ferias_vencimento
    //             ]);
    //         }

    //         return $funcionario->fresh();
    //     });
    // }

     /**
     * Atualizar funcionário completo
     */
    public function atualizarFuncionario(Funcionario $funcionario, array $dados): Funcionario
    {
        return DB::transaction(function () use ($funcionario, $dados) {
            // Atualiza dados básicos
            $funcionario->update($dados);

            // Atualiza relacionamentos
            $funcionario->endereco()->updateOrCreate(
                ['funcionario_id' => $funcionario->id],
                $dados
            );

            $funcionario->documentos()->updateOrCreate(
                ['funcionario_id' => $funcionario->id],
                $dados
            );

            $funcionario->contatos()->updateOrCreate(
                ['funcionario_id' => $funcionario->id],
                $dados
            );

            $funcionario->dadosBancarios()->updateOrCreate(
                ['funcionario_id' => $funcionario->id],
                $dados
            );

            // Contrato (se mudar data_admissao, recalcula férias automaticamente)
            $funcionario->contrato()->updateOrCreate(
                ['funcionario_id' => $funcionario->id],
                $dados
            );

            $funcionario->beneficios()->updateOrCreate(
                ['funcionario_id' => $funcionario->id],
                $dados
            );

            return $funcionario->fresh([
                'endereco', 'documentos', 'contatos',
                'dadosBancarios', 'contrato', 'beneficios'
            ]);
        });
    }

    /**
     * Validar idade mínima (14 anos para aprendiz, 16 para CLT)
     */
    private function validarIdadeMinima(string $dataNascimento): void
    {
        $idade = now()->diffInYears($dataNascimento);

        if ($idade < 14) {
            throw new \InvalidArgumentException('Funcionário deve ter pelo menos 14 anos.');
        }
    }

    /**
     * Validação básica de CPF (algoritmo de dígitos verificadores)
     */
    private function validarCPF(string $cpf): void
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11) {
            throw new \InvalidArgumentException('CPF deve ter 11 dígitos.');
        }

        // Verificar se não é uma sequência de números iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            throw new \InvalidArgumentException('CPF inválido.');
        }

        // Validação dos dígitos verificadores (algoritmo padrão)
        for ($i = 9; $i < 11; $i++) {
            $soma = 0;
            for ($j = 0; $j < $i; $j++) {
                $soma += $cpf[$j] * (($i + 1) - $j);
            }
            $resto = $soma % 11;
            $digito = $resto < 2 ? 0 : 11 - $resto;

            if ($cpf[$i] != $digito) {
                throw new \InvalidArgumentException('CPF inválido.');
            }
        }
    }
}