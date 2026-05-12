<?php

namespace App\Services\RH;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Domain\RH\{FolhaPagamento, Funcionario, Holerite};

class FolhaPagamentoService
{
    public function __construct(
        private CalculoTrabalhistaService $calculoService
    ) {}
    /**
     * Calcula o valor total de diárias para um funcionário em um período
     *
     * @param Funcionario $funcionario
     * @param Carbon|string $dataInicio
     * @param Carbon|string $dataFim
     * @return float
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
    ): Holerite {

        // Verifica se já existe holerite
        $holerite = $folha->holerites()
            ->where('funcionario_id', $funcionario->id)
            ->first();

        if ($holerite && !$recalcular) {
            return $holerite; // Já existe, não recalcula
        }

        // Calcula valores
        $salarioBruto = $this->obterSalarioBruto($funcionario, $folha->competencia);
        $inss = $this->calculoService->calcularInss($salarioBruto);
        $irrf = $this->calculoService->calcularIrrf($salarioBruto, $inss['valor'], $funcionario->dependentes ?? 0);
        $valeTransporte = $this->calculoService->calcularValeTransporte($salarioBruto, $funcionario->valor_vt ?? 0);

        $outrosDescontos = 0; // Implementar lógica específica depois

        $salarioLiquido = $salarioBruto - $inss['valor'] - $irrf['valor'] - $valeTransporte - $outrosDescontos;

        $dadosHolerite = [
            'folha_pagamento_id' => $folha->id,
            'funcionario_id' => $funcionario->id,
            'salario_bruto' => $salarioBruto,
            'inss_base' => $inss['base'],
            'inss_valor' => $inss['valor'],
            'inss_aliquota_aplicada' => $inss['aliquota_efetiva'],
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
    private function obterSalarioBruto(Funcionario $funcionario, string $competencia): float
    {
        // Por enquanto, pega o salário atual do funcionário
        // Futuramente pode considerar histórico de salários, afastamentos, etc.
        return $funcionario->salario ?? 0;
    }
}
