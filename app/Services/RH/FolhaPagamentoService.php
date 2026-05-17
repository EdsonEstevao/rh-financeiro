<?php

namespace App\Services\RH;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Models\Domain\RH\{FolhaLancamento, FolhaPagamento, Funcionario, Holerite};

class FolhaPagamentoService
{
    public function __construct(
        private CalculoTrabalhistaService $calculoService,
        protected FolhaLancamentoService $folhaLancamentoService
    ) {}
    /**
     * Calcula o valor total de diárias para um funcionário em um período
     *
     * @param Funcionario $funcionario
     * @param Carbon|string $dataInicio
     * @param Carbon|string $dataFim
     * @return float
     */



    /*
    ||----------------------------------------------------------------------
    || Calcula o valor total de diárias para um funcionário em um período
    ||----------------------------------------------------------------------
    */


    public function calcularFolhaDiarista(Funcionario $funcionario, $dataInicio, $dataFim): float
    {
        // Garantir objetos Carbon
        $dataInicio = Carbon::parse($dataInicio)->startOfDay();
        $dataFim   = Carbon::parse($dataFim)->endOfDay();

        $total = $funcionario->diarias()
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->whereIn('status', ['aprovada','paga'])
            ->sum('valor');

        return (float) $total;
    }


    /*
    ||----------------------------------------------------------------------
    || Cria uma nova folha de pagamento
    ||----------------------------------------------------------------------
    */

    /**
     * Cria uma nova folha de pagamento
     */
    public function criar(Funcionario $funcionario, Carbon $competencia, array $dados): FolhaPagamento
    {
        // Validação de negócio
        $this->validarNaoDuplicado($funcionario, $competencia);

        // Cria a folha primeiro
        $folha = FolhaPagamento::create([
            'funcionario_id' => $funcionario->id,
            'competencia' => $competencia->format('Y-m-d'),
            'salario_base' => $funcionario->salario_base,
            'quinto_dia_util' => $this->calculoService->calcularQuintoDiaUtil($competencia)->format('Y-m-d'),
            'observacao' => $dados['observacao'] ?? 'Folha gerada em ' . now()->format('d/m/Y H:i'),
            'status' => $dados['status'] ?? 'aberta',
        ]);

          // Gera os lançamentos detalhados
        $lancamentos = $this->folhaLancamentoService->gerarLancamentos($folha, $dados);

        // Calcula tudo
        // $resultado = $this->processarCalculos($funcionario, $competencia, $dados);
        // Atualiza os totais na folha baseado nos lançamentos
        $this->atualizarTotaisFolha($folha);

        // Persiste
        // return FolhaPagamento::create($resultado);
        return $folha->fresh();
    }

    /**
     * Atualiza uma folha existente
     */
    public function atualizar(FolhaPagamento $folha, array $dados): FolhaPagamento
    {
         // Atualiza dados básicos
        $folha->update([
            'observacao' => $dados['observacao'] ?? $folha->observacao,
            'status' => $dados['status'] ?? $folha->status,
        ]);

        // Regenera os lançamentos
        $this->folhaLancamentoService->gerarLancamentos($folha, $dados);

        // $funcionario = $folha->funcionario;
        // $competencia = Carbon::parse($folha->competencia);

        // // Recalcula tudo
        // $resultado = $this->processarCalculos($funcionario, $competencia, $dados);

         // Atualiza os totais
        $this->atualizarTotaisFolha($folha);



        // Atualiza
        // $folha->update($resultado);

        return $folha->fresh();
    }


    private function atualizarTotaisFolha(FolhaPagamento $folha): void
    {
        $resumo = $this->folhaLancamentoService->getResumo($folha);

        $folha->update([
            'salario_base' => $folha->funcionario->salario_base,
            'total_proventos' => $resumo['total_proventos'],
            'total_descontos' => $resumo['total_descontos'],
            'salario_liquido' => $resumo['total_liquido'],
        ]);
    }

    /**
     * Processa todos os cálculos e retorna array pronto para persistir
     */
    public function processarCalculos(Funcionario $funcionario, Carbon $competencia, array $dados): array
    {
        // Normaliza inputs
        $inputs = $this->normalizarInputs($dados);

        // 1. Jornada
        $valorHora = $this->calculoService->calcularValorHora($funcionario);
        $calendario = $this->calculoService->getResumoCalendario($competencia);

        // 2. Salário Base
        $salarioBase = $funcionario->salario_base ?? 0;

        // 3. Horas Extras (normais + sábado + feriado)
        $totalHorasExtrasNormais = round($inputs['horas_extras_totais'] * $valorHora['valor_hora_extra'], 2);
        $totalHorasSabado = round($inputs['horas_sabado'] * $valorHora['valor_hora_extra'], 2);
        $totalHorasFeriado = round($inputs['horas_feriado'] * $valorHora['valor_hora_feriado'], 2);
        $totalHorasExtras = $totalHorasExtrasNormais + $totalHorasSabado + $totalHorasFeriado;

        // 4. Total horas extras (soma das quantidades)
        $horasExtrasTotalQuantidade = $inputs['horas_extras_totais'] + $inputs['horas_sabado'] + $inputs['horas_feriado'];

        // 5. DSR Hora Extra
        $dsrHoraExtra = $this->calculoService->calcularDSR(
            $horasExtrasTotalQuantidade,
            $valorHora['valor_hora_extra'],
            $competencia
        );

        // 6. Salário Família
        $salarioFamilia = $this->calculoService->calcularSalarioFamilia($funcionario);

        // 7. Gratificação
        $gratificacao = $inputs['gratificacao_feriado'];

        // 8. Faltas
        $faltas = $this->calculoService->calcularFaltas(
            $inputs['faltas_dias'],
            $salarioBase,
            $competencia
        );

        // 9. INSS
        $inss = $this->calculoService->calcularINSS($salarioBase);

        // 10. Totais parciais
        $proventosParcial = $salarioBase + $totalHorasExtras + $dsrHoraExtra + $salarioFamilia + $gratificacao;
        $descontosParcial = $inss + $inputs['vale_dia_20'] + $inputs['vale_extra'] + $faltas['valor'] + $faltas['dsr'];

        // 11. Arredondamentos
        $arredondamentos = $this->calculoService->calcularArredondamentos($proventosParcial, $descontosParcial);

        // 12. Totais finais
        $totalProventos = round($proventosParcial + $arredondamentos['provento'], 2);
        $totalDescontos = round($descontosParcial + $arredondamentos['desconto'], 2);
        $totalLiquido = round($totalProventos - $totalDescontos, 2);

        return [
            'funcionario_id' => $funcionario->id,
            'competencia' => $competencia->format('Y-m-d'),
            'salario_base' => $salarioBase,
            'horas_extras_totais' => $inputs['horas_extras_totais'],
            'valor_hora_extra' => $valorHora['valor_hora_extra'],
            'gratificacao_feriado' => $gratificacao,
            'salario_familia_hr_extra' => $salarioFamilia,
            'arredondamento_provento' => $arredondamentos['provento'],
            'desconto_inss' => $inss,
            'vale_dia_20' => $inputs['vale_dia_20'],
            'vale_extra' => $inputs['vale_extra'],
            'faltas_valor' => $faltas['valor'],
            'dsr_faltas' => $faltas['dsr'],
            'arredondamento_desconto' => $arredondamentos['desconto'],
            'quinto_dia_util' => $this->calculoService->calcularQuintoDiaUtil($competencia)->format('Y-m-d'),
            'observacao' => $inputs['observacao'] ?? 'Folha gerada em ' . now()->format('d/m/Y H:i'),
            'status' => $inputs['status'] ?? 'aberta',
        ];
    }

    /**
     * Retorna dados calculados para exibição (sem persistir)
     */
    public function preview(Funcionario $funcionario, Carbon $competencia, array $dados): array
    {
        $calculos = $this->processarCalculos($funcionario, $competencia, $dados);

        return [
            'funcionario' => $funcionario->load('cargo'),
            'competencia' => $competencia->format('Y-m'),
            'jornada' => $this->calculoService->calcularValorHora($funcionario),
            'calendario' => $this->calculoService->getResumoCalendario($competencia),
            'folha' => $calculos,
            'totais' => [
                'proventos' => $calculos['salario_base'] +
                               ($calculos['horas_extras_totais'] * $calculos['valor_hora_extra']) +
                               $calculos['salario_familia_hr_extra'] +
                               $calculos['gratificacao_feriado'] +
                               $calculos['arredondamento_provento'],
                'descontos' => $calculos['desconto_inss'] +
                               $calculos['vale_dia_20'] +
                               $calculos['vale_extra'] +
                               $calculos['faltas_valor'] +
                               $calculos['dsr_faltas'] +
                               $calculos['arredondamento_desconto'],
            ],
        ];
    }

    // ─── MÉTODOS AUXILIARES ────────────────────────

    private function normalizarInputs(array $dados): array
    {
        return [
            'horas_extras_totais' => (float) ($dados['horas_extras_totais'] ?? 0),
            'horas_sabado' => (float) ($dados['horas_sabado'] ?? 0),
            'horas_feriado' => (float) ($dados['horas_feriado'] ?? 0),
            'faltas_dias' => (float) ($dados['faltas_dias'] ?? 0),
            'vale_dia_20' => (float) ($dados['vale_dia_20'] ?? 0),
            'vale_extra' => (float) ($dados['vale_extra'] ?? 0),
            'gratificacao_feriado' => (float) ($dados['gratificacao_feriado'] ?? 0),
            'status' => $dados['status'] ?? 'aberta',
            'observacao' => $dados['observacao'] ?? null,
        ];
    }

    // private function validarNaoDuplicado(Funcionario $funcionario, Carbon $competencia): void
    // {
    //     $existe = FolhaPagamento::where('funcionario_id', $funcionario->id)
    //         ->where('competencia', $competencia->format('Y-m-d'))
    //         ->exists();

    //     if ($existe) {
    //         throw new \InvalidArgumentException(
    //             "Já existe folha para {$funcionario->nome_completo} na competência {$competencia->format('m/Y')}!"
    //         );
    //     }
    // }

    private function validarNaoDuplicado(Funcionario $funcionario, Carbon $competencia): void
    {
        $existe = FolhaPagamento::where('funcionario_id', $funcionario->id)
            ->where('competencia', $competencia->format('Y-m-d'))
            ->exists();

        if ($existe) {
            throw new \InvalidArgumentException(
                "Já existe folha para {$funcionario->nome_completo} na competência {$competencia->format('m/Y')}!"
            );
        }
    }


    /*
    ||----------------------------------------------------------------------
    || Relatórios
    ||----------------------------------------------------------------------
    */



    /**
     * Gera relatório de folha de todos os diaristas em um período
     *
     * @param Carbon|string $dataInicio
     * @param Carbon|string $dataFim
     * @return array
     */
    public function gerarRelatorioDiaristas($dataInicio, $dataFim): array
    {
        $dataInicio = Carbon::parse($dataInicio)->startOfDay();
        $dataFim   = Carbon::parse($dataFim)->endOfDay();

        $diaristas = Funcionario::whereTrue('eh_diarista')->get();

        $relatorio = [];

        foreach ($diaristas as $funcionario) {
            $total = $this->calcularFolhaDiarista($funcionario, $dataInicio, $dataFim);

            $relatorio[] = [
                'funcionario_id' => $funcionario->id,
                'nome'           => $funcionario->nome,
                'valor_total'    => $total,
                'dias_trabalhados' => $funcionario->diarias()
                    ->whereBetween('data', [$dataInicio, $dataFim])
                    ->whereIn('status', ['aprovada','paga'])
                    ->count(),
            ];
        }

        return $relatorio;
    }



    /**
     * Cria ou busca folha de pagamento para uma competência
     */
    public function criarOuBuscarFolha(string $competencia): FolhaPagamento
    {
        $dataCompetencia = Carbon::parse($competencia)->startOfMonth();

        return FolhaPagamento::firstOrCreate([
            'competencia' => $dataCompetencia->toDateString(),
        ], [
            'status' => 'aberta',
        ]);
    }

    /**
     * Gera holerites para todos os funcionários ativos na competência
     */
    public function gerarHolerites(FolhaPagamento $folha, bool $recalcular = false): array
    {
        if ($folha->status === 'fechada' && !$recalcular) {
            throw ValidationException::withMessages([
                'folha' => ['Folha já está fechada. Use a opção recalcular se necessário.'],
            ]);
        }

        $funcionariosAtivos = Funcionario::ativo()
            ->whereDate('data_admissao', '<=', $folha->competencia)
            ->get();

        $resultados = [
            'processados' => 0,
            'erros' => [],
        ];

        DB::transaction(function () use ($folha, $funcionariosAtivos, $recalcular, &$resultados) {
            foreach ($funcionariosAtivos as $funcionario) {
                try {
                    $this->gerarHoleriteFuncionario($folha, $funcionario, $recalcular);
                    $resultados['processados']++;
                } catch (\Exception $e) {
                    $resultados['erros'][] = [
                        'funcionario' => $funcionario->nome,
                        'erro' => $e->getMessage(),
                    ];
                }
            }
        });

        return $resultados;
    }

    /**
     * Gera holerite para um funcionário específico
     */
    public function gerarHoleriteFuncionario(
        FolhaPagamento $folha,
        Funcionario $funcionario,
        bool $recalcular = false
    ): ?Holerite {

        // Verifica se já existe holerite
        $holerite = $folha->holerites()
            ->where('funcionario_id', $funcionario->id)
            ->first();

        if ($holerite && !$recalcular) {
            return $holerite; // Já existe, não recalcula
        }

        // Calcula valores
        $salarioBruto = $this->obterSalarioBruto($funcionario, $folha->competencia);
        $inss = $this->calculoService->calcularINSS($salarioBruto);
        $aliquotaEfetivaINSS = $this->calculoService->getAliquotaEfetivaINSS($salarioBruto);
        $irrf = $this->calculoService->calcularIrrf($salarioBruto, $inss, $funcionario->dependentes ?? 0);
        $valeTransporte = $this->calculoService->calcularValeTransporte($salarioBruto, $funcionario->valor_vt ?? 0);

        $outrosDescontos = 0; // Implementar lógica específica depois

        $salarioLiquido = $salarioBruto - $inss - $irrf['valor'] - $valeTransporte - $outrosDescontos;

        $dadosHolerite = [
            'folha_pagamento_id' => $folha->id,
            'funcionario_id' => $funcionario->id,
            'salario_bruto' => $salarioBruto,
            'inss_base' => $inss,
            'inss_valor' => $inss,
            'inss_aliquota_aplicada' => $aliquotaEfetivaINSS,
            'irrf_valor' => $irrf['valor'],
            'vt_valor' => $valeTransporte,
            'outros_descontos' => $outrosDescontos,
            'salario_liquido' => $salarioLiquido,
        ];

        if ($holerite) {
            // Atualiza existente
            $holerite->update($dadosHolerite);
            return $holerite->fresh();
        } else {
            // Cria novo
            return Holerite::create($dadosHolerite);
        }
    }

    /**
     * Fecha a folha (impede alterações)
     */
    public function fecharFolha(FolhaPagamento $folha): FolhaPagamento
    {
        if ($folha->holerites()->count() === 0) {
            throw ValidationException::withMessages([
                'folha' => ['Não é possível fechar folha sem holerites.'],
            ]);
        }

        $folha->update(['status' => 'fechada']);
        return $folha->fresh();
    }

    /**
     * Reabre a folha para alterações
     */
    public function reabrirFolha(FolhaPagamento $folha): FolhaPagamento
    {
        $folha->update(['status' => 'aberta']);
        return $folha->fresh();
    }

    /**
     * Obtém o salário bruto do funcionário para a competência
     * Por enquanto simples, mas pode evoluir para considerar aumentos, afastamentos, etc.
     */
    private function obterSalarioBruto(Funcionario $funcionario, ?string $competencia = null): float
    {
        // Por enquanto, pega o salário atual do funcionário
        // Futuramente pode considerar histórico de salários, afastamentos, etc.
        if ($competencia) {
            $funcionario->load(['cargo' => function ($query) use ($competencia) {
                $query->where('competencia', '<=', $competencia);
            }]);
        }

        return $funcionario->salario ?? 0;
    }
}