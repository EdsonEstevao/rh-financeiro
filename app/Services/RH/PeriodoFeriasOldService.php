<?php

namespace App\Services\RH;

use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\Domain\RH\{Funcionario, PeriodoFerias};

class PeriodoFeriasOldService
{
    /**
     * Cria um período de férias para o funcionário garantindo:
     * - datas válidas
     * - sem sobreposição com outros períodos (exceto cancelados)
     */
    public function criarPeriodo(Funcionario $funcionario, array $dados): PeriodoFerias
    {
        $inicio = Carbon::parse($dados['data_inicio'])->startOfDay();
        $fim    = Carbon::parse($dados['data_fim'])->startOfDay();

        if ($fim->lt($inicio)) {
            throw ValidationException::withMessages([
                'data_fim' => ['A data final deve ser maior ou igual à data inicial.'],
            ]);
        }

        $statusIgnorados = ['cancelada'];

        // Sobreposição (intervalos inclusivos):
        // existe sobreposição se: inicio <= fimExistente AND fim >= inicioExistente
        $existeSobreposicao = $funcionario->periodoFerias()
            ->whereNotIn('status', $statusIgnorados)
            ->whereDate('data_inicio', '<=', $fim->toDateString())
            ->whereDate('data_fim', '>=', $inicio->toDateString())
            ->exists();

        if ($existeSobreposicao) {
            throw ValidationException::withMessages([
                'data_inicio' => ['Já existe período de férias que conflita com as datas informadas.'],
                'data_fim'    => ['Já existe período de férias que conflita com as datas informadas.'],
            ]);
        }

        return $funcionario->periodoFerias()->create([
            'data_inicio'      => $inicio->toDateString(),
            'data_fim'         => $fim->toDateString(),
            'status'           => $dados['status'] ?? 'planejada',
            'abono_pecuniario' => (bool)($dados['abono_pecuniario'] ?? false),
            'observacao'       => $dados['observacao'] ?? null,
            'numero_periodo'   => (int)($dados['numero_periodo'] ?? 1),
        ]);
    }

    /**
     * Dias corridos do período (inclusivo).
     */
    public function diasDoPeriodo(PeriodoFerias $periodo): int
    {
        $inicio = Carbon::parse($periodo->data_inicio)->startOfDay();
        $fim    = Carbon::parse($periodo->data_fim)->startOfDay();

        return $inicio->diffInDays($fim) + 1;
    }
}
