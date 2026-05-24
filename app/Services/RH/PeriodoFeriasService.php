<?php

namespace App\Services\RH;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Domain\RH\{Funcionario, PeriodoFerias};

class PeriodoFeriasService
{
    /**
     * Cria um período de férias validando datas e sobreposição.
     */
    public function criarPeriodo(Funcionario $funcionario, array $dados): PeriodoFerias
    {
        // ✅ Observação padrão se não informada
        $dados['observacao'] = $dados['observacao'] 
            ?? 'Férias agendadas e aprovadas pelo gestor: ' . Auth::user()->name;

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

        // Valida período aquisitivo
        $this->validarDatasPeriodoAquisitivo($funcionario, $inicio, $fim);

        // Ignora sobreposição com o próprio tipo 'prevista'
        $ignorarTipo = ($dados['tipo'] ?? '') === 'prevista' ? 'prevista' : null;

        $this->validarSobreposicao($funcionario, $inicio, $fim, null, $ignorarTipo);

        // Número do período
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

        // ferias_vencimento pertence ao Funcionario
        if (isset($dados['ferias_vencimento'])) {
            $this->validarVencimentoFerias($funcionario, $fim, Carbon::parse($dados['ferias_vencimento']));
            $funcionario->update(['ferias_vencimento' => $dados['ferias_vencimento']]);
        }

        return $periodo;
    }

    /**
     * Atualiza um período existente validando sobreposição.
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

        // Carrega funcionário
        $funcionario = $periodo->relationLoaded('funcionario')
            ? $periodo->funcionario
            : $periodo->funcionario()->first();

        // Valida período aquisitivo
        $this->validarDatasPeriodoAquisitivo($funcionario, $inicio, $fim, $periodo);

        // Valida sobreposição
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
     * Valida se as datas estão dentro do período aquisitivo.
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

        // Prevista = sem validação
        if ($tipoPeriodo === 'prevista') {
            return;
        }

        // Efetiva = regras específicas
        if ($tipoPeriodo === 'efetiva') {
            if ($inicio->lt($dataAdmissao)) {
                throw ValidationException::withMessages([
                    'data_inicio' => ["A data de início não pode ser anterior à admissão ({$dataAdmissao->format('d/m/Y')})."],
                ]);
            }
            if ($fim->lt($dataAdmissao)) {
                throw ValidationException::withMessages([
                    'data_fim' => ["A data de fim não pode ser anterior à admissão ({$dataAdmissao->format('d/m/Y')})."],
                ]);
            }
            if ($inicio->gt($hoje)) {
                throw ValidationException::withMessages([
                    'data_inicio' => ["Férias efetivas não podem ter data futura. Use 'Programada'."],
                ]);
            }
            return;
        }

        // Programada = validações completas
        if ($inicio->lt($dataAdmissao)) {
            throw ValidationException::withMessages([
                'data_inicio' => ["Data de início anterior à admissão ({$dataAdmissao->format('d/m/Y')})."],
            ]);
        }

        if ($inicio->isSameDay($dataAdmissao)) {
            $proximoAno = $dataAdmissao->copy()->addYear();
            throw ValidationException::withMessages([
                'data_inicio' => ["Mesmo dia da admissão. Primeiro período: {$proximoAno->format('d/m/Y')}."],
            ]);
        }

        if ($inicio->year == $dataAdmissao->year && $inicio->month == $dataAdmissao->month) {
            $anoCorreto = $dataAdmissao->year + 1;
            throw ValidationException::withMessages([
                'data_inicio' => ["Mesmo mês/ano da admissão. Ano correto provável: {$anoCorreto}."],
            ]);
        }

        if ($fim->lt($dataAdmissao)) {
            throw ValidationException::withMessages([
                'data_fim' => ["Data de fim anterior à admissão ({$dataAdmissao->format('d/m/Y')})."],
            ]);
        }

        if ($inicio->year < $hoje->year || ($inicio->year == $hoje->year && $inicio->lt($hoje))) {
            throw ValidationException::withMessages([
                'data_inicio' => ["Data no passado. Para férias já gozadas, use tipo 'Efetiva'."],
            ]);
        }

        if ($inicio->gt($hoje->copy()->addYears(2))) {
            throw ValidationException::withMessages([
                'data_inicio' => ["Data muito distante. Limite de 2 anos para agendamento."],
            ]);
        }
    }

    /**
     * Valida data de vencimento das férias.
     */
    private function validarVencimentoFerias(Funcionario $funcionario, Carbon $fimFerias, Carbon $vencimento): void
    {
        if ($vencimento->lte($fimFerias)) {
            throw ValidationException::withMessages([
                'ferias_vencimento' => ["Vencimento deve ser após o fim das férias ({$fimFerias->format('d/m/Y')})."],
            ]);
        }

        if (!$funcionario->relationLoaded('contrato')) {
            $funcionario->load('contrato');
        }

        if ($funcionario->contrato && $funcionario->contrato->data_admissao) {
            $dataAdmissao = Carbon::parse($funcionario->contrato->data_admissao);
            $primeiroVencimento = $dataAdmissao->copy()->addYear();

            if ($vencimento->lt($primeiroVencimento)) {
                throw ValidationException::withMessages([
                    'ferias_vencimento' => ["Vencimento não pode ser antes de {$primeiroVencimento->format('d/m/Y')}."],
                ]);
            }
        }
    }

    /**
     * Valida sobreposição de períodos.
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
     * Calcula o próximo número de período.
     */
    private function calcularProximoNumeroPeriodo(Funcionario $funcionario): int
    {
        return ($funcionario->periodoFerias()->max('numero_periodo') ?? 0) + 1;
    }

    /**
     * Retorna dias corridos do período.
     */
    public function diasDoPeriodo(PeriodoFerias $periodo): int
    {
        return $periodo->diasCorridos();
    }

    /**
     * Calcula as férias proporcionais na demissão.
     */
    public function calcularFeriasRescisorias(Funcionario $funcionario, Carbon $dataDemissao): array
    {
        if (!$funcionario->relationLoaded('contrato')) {
            $funcionario->load('contrato');
        }

        $dataAdmissao = Carbon::parse($funcionario->contrato->data_admissao);
        $mesesTrabalhados = $dataAdmissao->diffInMonths($dataDemissao);
        
        // Período aquisitivo atual
        $periodoAtualInicio = $funcionario->periodo_aquisitivo_inicio 
            ? Carbon::parse($funcionario->periodo_aquisitivo_inicio) 
            : $dataAdmissao;
        
        $mesesPeriodoAtual = $periodoAtualInicio->diffInMonths($dataDemissao);

        $resultado = [
            'data_admissao'       => $dataAdmissao->format('d/m/Y'),
            'data_demissao'       => $dataDemissao->format('d/m/Y'),
            'meses_totais'        => $mesesTrabalhados,
            'meses_periodo_atual' => $mesesPeriodoAtual,
            'ferias_vencidas'     => $funcionario->ferias_vencidas,
            'ferias_em_dobro'     => $funcionario->ferias_em_dobro,
        ];

        // Férias vencidas (períodos anteriores não gozados)
        if ($funcionario->ferias_vencidas) {
            $resultado['dias_vencidos'] = 30; // 30 dias
            $resultado['valor_vencido'] = 'Integral + Dobro'; // Pagamento em dobro
        }

        // Férias proporcionais (período atual)
        $diasProporcionais = (int) floor(($mesesPeriodoAtual / 12) * 30);
        
        // Ajuste: mais de 14 dias trabalhados no mês = conta como mês cheio
        $diasUltimoMes = $periodoAtualInicio->copy()->addMonths($mesesPeriodoAtual)->diffInDays($dataDemissao);
        if ($diasUltimoMes > 14 && $diasProporcionais < 30) {
            $diasProporcionais += (int) floor(30 / 12); // +2.5 dias
        }

        $resultado['dias_proporcionais'] = min($diasProporcionais, 30);
        $resultado['total_dias_pagar'] = ($resultado['dias_vencidos'] ?? 0) + $resultado['dias_proporcionais'];

        return $resultado;
    }
}