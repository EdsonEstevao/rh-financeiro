<?php

namespace App\Services\RH;

use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\Domain\RH\{Funcionario, PeriodoFerias};

class PeriodoFeriasService
{
    /**
     * Cria um período de férias validando datas e sobreposição.
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

        // Ignora sobreposição com o próprio tipo 'prevista' ao recriar automaticamente
        $ignorarTipo = $dados['tipo'] === 'prevista' ? 'prevista' : null;

        $this->validarSobreposicao($funcionario, $inicio, $fim, null, $ignorarTipo);

        // numero_periodo: usa o informado ou calcula automaticamente
        $numeroPeriodo = isset($dados['numero_periodo'])
            ? (int)$dados['numero_periodo']
            : (($funcionario->periodoFerias()->max('numero_periodo') ?? 0) + 1);

        return $funcionario->periodoFerias()->create([
            'data_inicio'       => $inicio->toDateString(),
            'data_fim'          => $fim->toDateString(),
            'tipo'              => $dados['tipo']              ?? 'programada',
            'status'            => $dados['status']            ?? 'planejada',
            'abono_pecuniario'  => (bool)($dados['abono_pecuniario']  ?? false),
            'observacao'        => $dados['observacao']        ?? null,
            'numero_periodo'    => $numeroPeriodo,
            'ferias_vencimento' => $dados['ferias_vencimento'] ?? null, // ✅
        ]);
    }

    /**
     * Atualiza um período existente validando sobreposição (exceto consigo mesmo).
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

        // ✅ Carrega o relacionamento para evitar N+1
        $funcionario = $periodo->funcionario ?? $periodo->funcionario()->first();

        $this->validarSobreposicao($funcionario, $inicio, $fim, $periodo->id);

        $periodo->update([
            'data_inicio'       => $inicio->toDateString(),
            'data_fim'          => $fim->toDateString(),
            'tipo'              => $dados['tipo']             ?? $periodo->tipo,
            'status'            => $dados['status']           ?? $periodo->status,
            'abono_pecuniario'  => isset($dados['abono_pecuniario'])
                                    ? (bool)$dados['abono_pecuniario']
                                    : $periodo->abono_pecuniario,
            'observacao'        => $dados['observacao']       ?? $periodo->observacao,
            'numero_periodo'    => isset($dados['numero_periodo'])
                                    ? (int)$dados['numero_periodo']
                                    : $periodo->numero_periodo,

        ]);

        $periodo->funcionario->update([
            'ferias_vencimento' => $dados['ferias_vencimento'] ?? $periodo->ferias_vencimento, // ✅
        ]);

        return $periodo->fresh();
    }

    /**
     * Valida sobreposição de períodos de férias.
     *
     * @param Funcionario $funcionario
     * @param Carbon      $inicio
     * @param Carbon      $fim
     * @param int|null    $excluirId    — ignora o próprio registro ao atualizar
     * @param string|null $ignorarTipo  — ignora períodos de determinado tipo (ex: 'prevista')
     */
    private function validarSobreposicao(
        Funcionario $funcionario,
        Carbon $inicio,
        Carbon $fim,
        ?int $excluirId = null,
        ?string $ignorarTipo = null
    ): void {
        $query = $funcionario->periodoFerias()
            ->whereNotIn('status', ['cancelada'])
            ->whereDate('data_inicio', '<=', $fim->toDateString())
            ->whereDate('data_fim', '>=', $inicio->toDateString());

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        // ✅ Permite ignorar tipo específico (ex: prevista sendo recriada)
        if ($ignorarTipo) {
            $query->where('tipo', '!=', $ignorarTipo);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'data_inicio' => ['Já existe período de férias que conflita com as datas informadas.'],
                'data_fim'    => ['Já existe período de férias que conflita com as datas informadas.'],
            ]);
        }
    }

    /**
     * Retorna dias corridos do período (inclusivo).
     */
    public function diasDoPeriodo(PeriodoFerias $periodo): int
    {
        return $periodo->diasCorridos();
    }
}