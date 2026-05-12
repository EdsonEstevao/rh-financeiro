<?php

namespace App\Services\RH;

use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\Domain\RH\{Funcionario, PeriodoFerias};

class PeriodoFeriasService
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

        $this->validarSobreposicao($funcionario, $inicio, $fim);

        return $funcionario->periodosFerias()->create([
            'data_inicio'      => $inicio->toDateString(),
            'data_fim'         => $fim->toDateString(),
            'tipo'             => $dados['tipo'] ?? 'programada',
            'status'           => $dados['status'] ?? 'planejada',
            'abono_pecuniario' => (bool)($dados['abono_pecuniario'] ?? false),
            'observacao'       => $dados['observacao'] ?? null,
            'numero_periodo'   => (int)($dados['numero_periodo'] ?? 1),
        ]);
    }

    /**
     * Atualiza um período existente validando sobreposição (exceto consigo mesmo)
     */
    public function atualizarPeriodo(PeriodoFerias $periodo, array $dados): PeriodoFerias
    {
        $inicio = Carbon::parse($dados['data_inicio'])->startOfDay();
        $fim    = Carbon::parse($dados['data_fim'])->startOfDay();

        if ($fim->lt($inicio)) {
            throw ValidationException::withMessages([
                'data_fim' => ['A data final deve ser maior ou igual à data inicial.'],
            ]);
        }

        $this->validarSobreposicao($periodo->funcionario, $inicio, $fim, $periodo->id);

        $periodo->update([
            'data_inicio'      => $inicio->toDateString(),
            'data_fim'         => $fim->toDateString(),
            'tipo'             => $dados['tipo'] ?? $periodo->tipo,
            'status'           => $dados['status'] ?? $periodo->status,
            'abono_pecuniario' => isset($dados['abono_pecuniario']) ? (bool)$dados['abono_pecuniario'] : $periodo->abono_pecuniario,
            'observacao'       => $dados['observacao'] ?? $periodo->observacao,
            'numero_periodo'   => isset($dados['numero_periodo']) ? (int)$dados['numero_periodo'] : $periodo->numero_periodo,
        ]);

        return $periodo->fresh();
    }

    /**
     * Valida sobreposição de períodos
     */
    private function validarSobreposicao(Funcionario $funcionario, Carbon $inicio, Carbon $fim, ?int $excluirId = null): void
    {
        $statusIgnorados = ['cancelada'];

        $query = $funcionario->periodosFerias()
            ->whereNotIn('status', $statusIgnorados)
            ->whereDate('data_inicio', '<=', $fim->toDateString())
            ->whereDate('data_fim', '>=', $inicio->toDateString());

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'data_inicio' => ['Já existe período de férias que conflita com as datas informadas.'],
                'data_fim'    => ['Já existe período de férias que conflita com as datas informadas.'],
            ]);
        }
    }

    /**
     * Dias corridos do período (inclusivo).
     */
    public function diasDoPeriodo(PeriodoFerias $periodo): int
    {
        return $periodo->diasCorridos();
    }
}
