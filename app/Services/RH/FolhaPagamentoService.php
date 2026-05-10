<?php

namespace App\Services\RH;

use Carbon\Carbon;

use App\Models\Domain\RH\Funcionario;

class FolhaPagamentoService
{
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
}