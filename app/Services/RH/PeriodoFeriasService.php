<?php
namespace App\Services\RH;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\Domain\RH\{Funcionario, PeriodoFerias};

class PeriodoFeriasService
{
    /**
     * Cria um período de férias validando datas e sobreposição.
     */
    public function criarPeriodo2(Funcionario $funcionario, array $dados): PeriodoFerias
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
    public function atualizarPeriodo2(PeriodoFerias $periodo, array $dados): PeriodoFerias
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
    private function validarSobreposicao2(
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
    public function diasDoPeriodo2(PeriodoFerias $periodo): int
    {
        return $periodo->diasCorridos();
    }



    /**
     * Cria um período de férias validando datas e sobreposição.
     */
    public function criarPeriodo3(Funcionario $funcionario, array $dados): PeriodoFerias
    {
        if (empty($dados['data_inicio']) || empty($dados['data_fim'])) {
            throw ValidationException::withMessages([
                'data_inicio' => ['Data inicial é obrigatória.'],
                'data_fim' => ['Data final é obrigatória.'],
            ]);
        }

        $inicio = Carbon::parse($dados['data_inicio'])->startOfDay();
        $fim    = Carbon::parse($dados['data_fim'])->startOfDay();

        if ($fim->lt($inicio)) {
            throw ValidationException::withMessages([
                'data_fim' => ['A data final deve ser maior ou igual à data inicial.'],
            ]);
        }

        // Ignora sobreposição com o próprio tipo 'prevista' ao recriar automaticamente
        $ignorarTipo = ($dados['tipo'] ?? '') === 'prevista' ? 'prevista' : null;

        $this->validarSobreposicao($funcionario, $inicio, $fim, null, $ignorarTipo);

        // numero_periodo: usa o informado ou calcula automaticamente
        $numeroPeriodo = isset($dados['numero_periodo'])
            ? (int)$dados['numero_periodo']
            : $this->calcularProximoNumeroPeriodo($funcionario);

        $periodo = $funcionario->periodoFerias()->create([
            'data_inicio'      => $inicio->toDateString(),
            'data_fim'         => $fim->toDateString(),
            'tipo'             => $dados['tipo']             ?? 'programada',
            'status'           => $dados['status']           ?? 'planejada',
            'abono_pecuniario' => (bool)($dados['abono_pecuniario'] ?? false),
            'observacao'       => $dados['observacao']       ?? null,
            'numero_periodo'   => $numeroPeriodo,
        ]);

        // ferias_vencimento pertence ao Funcionario, não ao PeriodoFerias
        if (isset($dados['ferias_vencimento'])) {
            $funcionario->update(['ferias_vencimento' => $dados['ferias_vencimento']]);
        }

        return $periodo;
    }

    /**
     * Atualiza um período existente validando sobreposição (exceto consigo mesmo).
     */
    public function atualizarPeriodo3(PeriodoFerias $periodo, array $dados): PeriodoFerias
    {
        $inicio = Carbon::parse($dados['data_inicio'])->startOfDay();
        $fim    = Carbon::parse($dados['data_fim'])->startOfDay();

        if ($fim->lt($inicio)) {
            throw ValidationException::withMessages([
                'data_fim' => ['A data final deve ser maior ou igual à data inicial.'],
            ]);
        }

        // Carrega o relacionamento para evitar N+1
        $funcionario = $periodo->relationLoaded('funcionario')
            ? $periodo->funcionario
            : $periodo->funcionario()->first();

        $this->validarSobreposicao($funcionario, $inicio, $fim, $periodo->id);

        $periodo->update([
            'data_inicio'      => $inicio->toDateString(),
            'data_fim'         => $fim->toDateString(),
            'tipo'             => $dados['tipo']             ?? $periodo->tipo,
            'status'           => $dados['status']           ?? $periodo->status,
            'abono_pecuniario' => isset($dados['abono_pecuniario'])
                                    ? (bool)$dados['abono_pecuniario']
                                    : $periodo->abono_pecuniario,
            'observacao'       => $dados['observacao']       ?? $periodo->observacao,
            'numero_periodo'   => isset($dados['numero_periodo'])
                                    ? (int)$dados['numero_periodo']
                                    : $periodo->numero_periodo,
        ]);

        // ferias_vencimento pertence ao Funcionario
        if (isset($dados['ferias_vencimento'])) {
            $funcionario->update(['ferias_vencimento' => $dados['ferias_vencimento']]);
        }

        return $periodo->fresh();
    }

    /**
     * Valida sobreposição de períodos de férias.
     */
    private function validarSobreposicao3(
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
     * Calcula o próximo número de período para o funcionário.
     */
    private function calcularProximoNumeroPeriodo3(Funcionario $funcionario): int
    {
        return ($funcionario->periodoFerias()->max('numero_periodo') ?? 0) + 1;
    }

    /**
     * Retorna dias corridos do período (inclusivo).
     */
    public function diasDoPeriodo3(PeriodoFerias $periodo): int
    {
        return $periodo->diasCorridos();
    }
    /*----------------------------------*/
     /**
     * Cria um período de férias validando datas e sobreposição.
     */
    public function criarPeriodo(Funcionario $funcionario, array $dados): PeriodoFerias
    {
        $dados['observacao'] = $dados['observacao'] ?? 'Férias agendadas e aprovadas pelo gestor: ' . Auth::user()->name;
        if (empty($dados['data_inicio']) || empty($dados['data_fim'])) {
            throw ValidationException::withMessages([
                'data_inicio' => ['Data inicial é obrigatória.'],
                'data_fim' => ['Data final é obrigatória.'],
            ]);
        }

        $inicio = Carbon::parse($dados['data_inicio'])->startOfDay();
        $fim    = Carbon::parse($dados['data_fim'])->startOfDay();

        if ($fim->lt($inicio)) {
            throw ValidationException::withMessages([
                'data_fim' => ['A data final deve ser maior ou igual à data inicial.'],
            ]);
        }

        // ✅ Valida se as datas são compatíveis com o período aquisitivo
        $this->validarDatasPeriodoAquisitivo($funcionario, $inicio, $fim);

        // Ignora sobreposição com o próprio tipo 'prevista' ao recriar automaticamente
        $ignorarTipo = ($dados['tipo'] ?? '') === 'prevista' ? 'prevista' : null;

        $this->validarSobreposicao($funcionario, $inicio, $fim, null, $ignorarTipo);

        // numero_periodo: usa o informado ou calcula automaticamente
        $numeroPeriodo = isset($dados['numero_periodo'])
            ? (int)$dados['numero_periodo']
            : $this->calcularProximoNumeroPeriodo($funcionario);

        $periodo = $funcionario->periodoFerias()->create([
            'data_inicio'      => $inicio->toDateString(),
            'data_fim'         => $fim->toDateString(),
            'tipo'             => $dados['tipo']             ?? 'programada',
            'status'           => $dados['status']           ?? 'planejada',
            'abono_pecuniario' => (bool)($dados['abono_pecuniario'] ?? false),
            'observacao'       => $dados['observacao']       ?? null,
            'numero_periodo'   => $numeroPeriodo,
        ]);

        // ferias_vencimento pertence ao Funcionario, não ao PeriodoFerias
        if (isset($dados['ferias_vencimento'])) {
            $this->validarVencimentoFerias($funcionario, $fim, Carbon::parse($dados['ferias_vencimento']));
            $funcionario->update(['ferias_vencimento' => $dados['ferias_vencimento']]);
        }

        return $periodo;
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

        // Carrega o relacionamento para evitar N+1
        $funcionario = $periodo->relationLoaded('funcionario')
            ? $periodo->funcionario
            : $periodo->funcionario()->first();

        // ✅ Valida se as datas são compatíveis com o período aquisitivo
        $this->validarDatasPeriodoAquisitivo($funcionario, $inicio, $fim, $periodo);

        $this->validarSobreposicao($funcionario, $inicio, $fim, $periodo->id);

        $periodo->update([
            'data_inicio'      => $inicio->toDateString(),
            'data_fim'         => $fim->toDateString(),
            'tipo'             => $dados['tipo']             ?? $periodo->tipo,
            'status'           => $dados['status']           ?? $periodo->status,
            'abono_pecuniario' => isset($dados['abono_pecuniario'])
                                    ? (bool)$dados['abono_pecuniario']
                                    : $periodo->abono_pecuniario,
            'observacao'       => $dados['observacao']       ?? $periodo->observacao,
            'numero_periodo'   => isset($dados['numero_periodo'])
                                    ? (int)$dados['numero_periodo']
                                    : $periodo->numero_periodo,
        ]);

        // ferias_vencimento pertence ao Funcionario
        if (isset($dados['ferias_vencimento'])) {
            $this->validarVencimentoFerias($funcionario, $fim, Carbon::parse($dados['ferias_vencimento']));
            $funcionario->update(['ferias_vencimento' => $dados['ferias_vencimento']]);
        }

        return $periodo->fresh();
    }

    /**
     * ✅ NOVO: Valida se as datas do período estão dentro do período aquisitivo
     * e se não são anteriores à data de admissão.
     */
    private function validarDatasPeriodoAquisitivo(
    Funcionario $funcionario,
    Carbon $inicio,
    Carbon $fim,
    ?PeriodoFerias $periodoExistente = null
    ): void {
        if (!$funcionario->relationLoaded('contrato')) {
            $funcionario->load('contrato');
        }

        $contrato = $funcionario->contrato;

        if (!$contrato || !$contrato->data_admissao) {
            return;
        }

        $dataAdmissao = Carbon::parse($contrato->data_admissao)->startOfDay();
        $tipoPeriodo = $periodoExistente ? $periodoExistente->tipo : 'programada';
        $hoje = now()->startOfDay();

        // ✅ 1. Se for prevista, sai sem validar
        if ($tipoPeriodo === 'prevista') {
            return;
        }

        // ✅ 2. Se for EFETIVA, aplica regras específicas (ANTES das outras validações!)
        if ($tipoPeriodo === 'efetiva') {
            // Não pode ser antes da admissão
            if ($inicio->lt($dataAdmissao)) {
                throw ValidationException::withMessages([
                    'data_inicio' => [
                        "A data de início ({$inicio->format('d/m/Y')}) não pode ser anterior à data de admissão ({$dataAdmissao->format('d/m/Y')})."
                    ],
                ]);
            }

            // Data de fim também não pode ser antes da admissão
            if ($fim->lt($dataAdmissao)) {
                throw ValidationException::withMessages([
                    'data_fim' => [
                        "A data de fim ({$fim->format('d/m/Y')}) não pode ser anterior à data de admissão ({$dataAdmissao->format('d/m/Y')})."
                    ],
                ]);
            }

            // Não pode ter data futura (efetiva = já aconteceu)
            if ($inicio->gt($hoje)) {
                throw ValidationException::withMessages([
                    'data_inicio' => [
                        "Férias efetivas não podem ter data de início futura ({$inicio->format('d/m/Y')}). " .
                        "Use tipo 'Programada' para agendar férias futuras."
                    ],
                ]);
            }

            return; // ✅ OK para efetiva, sai do método
        }

        // ✅ 3. Daqui pra baixo, só vale para PROGRAMADA

        // ❌ Data de início menor que admissão
        if ($inicio->lt($dataAdmissao)) {
            throw ValidationException::withMessages([
                'data_inicio' => [
                    "A data de início não pode ser anterior à data de admissão ({$dataAdmissao->format('d/m/Y')})."
                ],
            ]);
        }

        // ❌ Mesmo dia da admissão
        if ($inicio->isSameDay($dataAdmissao)) {
            $proximoAno = $dataAdmissao->copy()->addYear();
            throw ValidationException::withMessages([
                'data_inicio' => [
                    "A data de início não pode ser no mesmo dia da admissão ({$dataAdmissao->format('d/m/Y')}). " .
                    "O primeiro período aquisitivo começa em {$proximoAno->format('d/m/Y')}."
                ],
            ]);
        }

        // ❌ Mesmo mês/ano da admissão (provável erro de digitação)
        if ($inicio->year == $dataAdmissao->year && $inicio->month == $dataAdmissao->month) {
            $anoCorreto = $dataAdmissao->year + 1;
            throw ValidationException::withMessages([
                'data_inicio' => [
                    "A data informada ({$inicio->format('d/m/Y')}) está no mesmo mês/ano da admissão. " .
                    "Provavelmente o ano correto é {$anoCorreto}. " .
                    "O primeiro período aquisitivo inicia em " . $dataAdmissao->copy()->addYear()->format('d/m/Y') . "."
                ],
            ]);
        }

        // ❌ Data de fim menor que admissão
        if ($fim->lt($dataAdmissao)) {
            throw ValidationException::withMessages([
                'data_fim' => [
                    "A data de fim não pode ser anterior à data de admissão ({$dataAdmissao->format('d/m/Y')})."
                ],
            ]);
        }

        // ❌ Data no passado para tipo programada
        if ($inicio->year < $hoje->year || ($inicio->year == $hoje->year && $inicio->lt($hoje))) {
            throw ValidationException::withMessages([
                'data_inicio' => [
                    "A data de início ({$inicio->format('d/m/Y')}) está no passado. " .
                    "Se for para registrar férias já gozadas, altere o tipo para 'Efetiva' e o status para 'Gozada'."
                ],
            ]);
        }

        // ❌ Data futura muito distante (mais de 2 anos)
        if ($inicio->gt($hoje->copy()->addYears(2))) {
            throw ValidationException::withMessages([
                'data_inicio' => [
                    "A data de início está muito distante ({$inicio->format('d/m/Y')}). " .
                    "O limite para agendamento é de 2 anos. Verifique se o ano está correto."
                ],
            ]);
        }
    }

    /**
     * ✅ NOVO: Valida data de vencimento das férias
     */
    private function validarVencimentoFerias(Funcionario $funcionario, Carbon $fimFerias, Carbon $vencimento): void
    {
        // Vencimento deve ser após o fim das férias
        if ($vencimento->lte($fimFerias)) {
            throw ValidationException::withMessages([
                'ferias_vencimento' => [
                    "A data de vencimento deve ser posterior ao fim das férias ({$fimFerias->format('d/m/Y')})."
                ],
            ]);
        }

        // Carrega contrato se necessário
        if (!$funcionario->relationLoaded('contrato')) {
            $funcionario->load('contrato');
        }

        if ($funcionario->contrato && $funcionario->contrato->data_admissao) {
            $dataAdmissao = Carbon::parse($funcionario->contrato->data_admissao);

            // Vencimento não pode ser antes da admissão + 1 ano
            $primeiroVencimento = $dataAdmissao->copy()->addYear();
            if ($vencimento->lt($primeiroVencimento)) {
                throw ValidationException::withMessages([
                    'ferias_vencimento' => [
                        "O vencimento não pode ser anterior a {$primeiroVencimento->format('d/m/Y')} " .
                        "(1 ano após a admissão)."
                    ],
                ]);
            }
        }
    }

    /**
     * Valida sobreposição de períodos de férias.
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
     * Calcula o próximo número de período para o funcionário.
     */
    private function calcularProximoNumeroPeriodo(Funcionario $funcionario): int
    {
        return ($funcionario->periodoFerias()->max('numero_periodo') ?? 0) + 1;
    }

    /**
     * Retorna dias corridos do período (inclusivo).
     */
    public function diasDoPeriodo(PeriodoFerias $periodo): int
    {
        return $periodo->diasCorridos();
    }

}