<?php

namespace App\Services\RH;

use Illuminate\Support\Facades\{Cache, Log};
use Carbon\Carbon;

use App\Models\Domain\RH\{Funcionario, TabelaInss, TabelaIrrf};

class CalculoTrabalhistaService
{
    // Propriedades de cache em memória (para não bater no banco repetidamente)
    private ?array $faixasInssCache            = null;
    private ?array $faixasIrrfCache             = null;
    private ?float $deducaoDependenteIrrfCache = null;

    // ─── CONSTANTES DE REGRA DE NEGÓCIO ────────────
    // (não mudam por tabela — são definidas por lei/CLT)

    private const HORAS_MENSAIS_PADRAO        = 220;  // 44h semanais
    private const PERCENTUAL_HORA_EXTRA       = 0.50; // 50%
    private const PERCENTUAL_HORA_FERIADO     = 1.00; // 100%
    private const PERCENTUAL_VALE_TRANSPORTE  = 0.06; // 6%
    private const PERCENTUAL_FGTS             = 0.08; // 8%


    // Dias padrão (seg-sex) - usado como fallback
    private const DIAS_TRABALHO_PADRAO = [1, 2, 3, 4, 5]; // Seg-Sex


    // Cache TTL: 1 hora (evita ir ao banco toda requisição)
    private const CACHE_TTL = 3600;

    // ─── FERIADOS FIXOS ────────────────────────────

    private const FERIADOS_FIXOS = [
        '01-01', // Confraternização Universal
        '21-04', // Tiradentes
        '01-05', // Dia do Trabalhador
        '07-09', // Independência
        '12-10', // N. Sra. Aparecida
        '02-11', // Finados
        '15-11', // Proclamação da República
        '25-12', // Natal
    ];

    // ═══════════════════════════════════════════════
    //  🆕 TIPOS DE CONTRATO QUE NÃO RECOLHEM
    // ═══════════════════════════════════════════════

    // tipo_contrato (Prazo/Termo)
    private const TIPOS_SEM_INSS = ['estagio'];
    private const TIPOS_SEM_IRRF = ['estagio'];
    private const TIPOS_SEM_FGTS = ['estagio', 'aprendiz'];
    private const TIPOS_SEM_SALARIO_FAMILIA = ['estagio'];

    // tipo_contratacao (Regime/Vínculo)
    private const REGIMES_SEM_INSS = ['pj', 'autonomo'];
    private const REGIMES_SEM_IRRF = ['pj', 'autonomo'];
    private const REGIMES_SEM_FGTS = ['pj', 'autonomo', 'avulso', 'estatutario'];

    // ═══════════════════════════════════════════════
    //  🆕 MÉTODO AUXILIAR: OBTER DIAS DE TRABALHO
    // ═══════════════════════════════════════════════

    /**
     * Obtém os dias de trabalho do funcionário a partir do contrato.
     * Se não houver contrato ou não estiver definido, usa seg-sex como padrão.
     */
    private function getDiasTrabalho(Funcionario $funcionario): array
    {
        $contrato = $funcionario->contrato;

        // if ($contrato && !empty($contrato->dias_trabalho)) {
        //     return $contrato->dias_trabalho;
        // }
        if ($contrato && !empty($contrato->dias_trabalho)) {
            $dias = is_string($contrato->dias_trabalho)
                ? json_decode($contrato->dias_trabalho, true)
                : $contrato->dias_trabalho;

            if (!empty($dias)) {
                return array_map('intval', $dias);
            }
        }

        return self::DIAS_TRABALHO_PADRAO;
    }

    /**
     * Verifica se é estagiário.
     */
    private function isEstagiario(Funcionario $funcionario): bool
    {
        $contrato = $funcionario->contrato;
        return $contrato && $contrato->tipo_contrato === 'estagio';
    }

    /**
     * Verifica se é aprendiz.
     */
    private function isAprendiz(Funcionario $funcionario): bool
    {
        $contrato = $funcionario->contrato;
        return $contrato && $contrato->tipo_contrato === 'aprendiz';
    }

    /**
     * Deve aplicar INSS?
     * Regra: verifica tipo_contrato + tipo_contratacao + flag aplica_inss
     */
    public function deveAplicarINSS(Funcionario $funcionario): bool
    {
        $contrato = $funcionario->contrato;
        if (!$contrato) return false;

        // Se o contrato explicitamente desmarca, não aplica
        if (!$contrato->aplica_inss) {
            return false;
        }

        // Verifica pelo tipo_contrato (estagio)
        if (in_array($contrato->tipo_contrato, self::TIPOS_SEM_INSS)) {
            return false;
        }

        // Verifica pelo tipo_contratacao (pj, autonomo)
        if (in_array($contrato->tipo_contratacao, self::REGIMES_SEM_INSS)) {
            return false;
        }

        return true;
    }

    /**
     * Deve aplicar IRRF?
     */
    private function deveAplicarIRRF(Funcionario $funcionario): bool
    {
        $contrato = $funcionario->contrato;
        if (!$contrato) return false;

        if (in_array($contrato->tipo_contrato, self::TIPOS_SEM_IRRF)) {
            return false;
        }

        if (in_array($contrato->tipo_contratacao, self::REGIMES_SEM_IRRF)) {
            return false;
        }

        return true;
    }

    /**
     * Deve calcular FGTS?
     */
    public function deveAplicarFGTS(Funcionario $funcionario): bool
    {
        $contrato = $funcionario->contrato;
        if (!$contrato) return false;

        if (in_array($contrato->tipo_contrato, self::TIPOS_SEM_FGTS)) {
            // Aprendiz tem FGTS reduzido (2%), mas se quiser simplificar:
            // return $contrato->tipo_contrato === 'aprendiz'; // 2%
            return false;
        }

        // 🆕 PJ, autônomo, avulso, estatutário
        if (in_array($contrato->tipo_contratacao, self::REGIMES_SEM_FGTS)) {
            return false;
        }

        return true;
    }

    /**
     * Deve calcular salário família?
     */
    private function deveAplicarSalarioFamilia(Funcionario $funcionario): bool
    {
        $contrato = $funcionario->contrato;
        if (!$contrato) return false;

        if (in_array($contrato->tipo_contrato, self::TIPOS_SEM_SALARIO_FAMILIA)) {
            return false;
        }

        return true;
    }

    /**
     * Pode fazer hora extra?
     * Estagiário: máximo 6h/dia, 30h/semana - não pode hora extra
     * Aprendiz: máximo 6h/dia - não pode hora extra
     */
    public function podeFazerHoraExtra(Funcionario $funcionario): bool
    {
        $contrato = $funcionario->contrato;
        if (!$contrato) return false;

        $tiposSemHE = ['estagio', 'aprendiz'];
        return !in_array($contrato->tipo_contrato, $tiposSemHE);
    }

    // ───────────────────────────────────────────────
    //  JORNADA DE TRABALHO
    // ───────────────────────────────────────────────

    public function calcularValorHora(Funcionario $funcionario): array
    {
        // $contrato = $funcionario->contrato;
        // $salarioBase  = $contrato->salario_base ?? 0;
        // $cargaHoraria = $contrato->carga_horaria_semanal ?? 44;
        // $horasMensais = ($cargaHoraria / 6) * 30;

        // $valorHoraNormal  = $horasMensais > 0 ? $salarioBase / $horasMensais : 0;
        // $valorHoraExtra   = $valorHoraNormal * (1 + self::PERCENTUAL_HORA_EXTRA);
        // $valorHoraFeriado = $valorHoraNormal * (1 + self::PERCENTUAL_HORA_FERIADO);

        // return [
        //     'horas_mensais'      => round($horasMensais, 2),
        //     'valor_hora_normal'  => round($valorHoraNormal, 2),
        //     'valor_hora_extra'   => round($valorHoraExtra, 2),
        //     'valor_hora_feriado' => round($valorHoraFeriado, 2),
        // ];
        $contrato = $funcionario->contrato;
        $salarioBase  = (float) $contrato->salario_base;
        $cargaHoraria = (int) $contrato->carga_horaria_semanal;
        $horasMensais = ($cargaHoraria / 6) * 30;

        $valorHoraNormal  = $horasMensais > 0 ? $salarioBase / $horasMensais : 0;
        $valorHoraExtra   = $valorHoraNormal * 1.5;
        $valorHoraFeriado = $valorHoraNormal * 2;

        return [
            'horas_mensais'      => round($horasMensais, 2),
            // 🆕 6 casas para precisão (igual ao JavaScript)
            'valor_hora_normal'  => round($valorHoraNormal, 6),
            'valor_hora_extra'   => round($valorHoraExtra, 6),
            'valor_hora_feriado' => round($valorHoraFeriado, 6),
        ];
    }

    // ───────────────────────────────────────────────
    //  INSS (PROGRESSIVO — TABELA DO BANCO)
    // ───────────────────────────────────────────────

    /**
     * Calcula INSS progressivo com base na tabela ativa vigente.
     */
    public function calcularINSS(float $salarioBase, ?Carbon $dataReferencia = null): float
    {
        $faixas = $this->getFaixasInssAtivas($dataReferencia);
        if (empty($faixas)) return 0;

        foreach ($faixas as $faixa) {
            $teto = (float) ($faixa['teto'] ?? PHP_FLOAT_MAX);
            
            if ($salarioBase <= $teto) {
                $aliquota = (float) $faixa['aliquota'];
                $deducao = (float) ($faixa['deducao'] ?? 0);
                $inss = ($salarioBase * $aliquota) - $deducao;
                
                // 🆕 Trunca em vez de arredondar
                return floor($inss * 100) / 100;
            }
        }

        // Última faixa
        $ultimaFaixa = end($faixas);
        $inss = ($salarioBase * (float) $ultimaFaixa['aliquota']) - (float) ($ultimaFaixa['deducao'] ?? 0);
        return floor($inss * 100) / 100;
    }

    /**
     * Alíquota efetiva do INSS (%).
     */
    public function getAliquotaEfetivaINSS(float $salarioBase, ?Carbon $dataReferencia = null): float
    {
        $inss = $this->calcularINSS($salarioBase, $dataReferencia);

        return $salarioBase > 0
            ? round(($inss / $salarioBase) * 100, 2)
            : 0;
    }

    // ───────────────────────────────────────────────
    //  IRRF (TABELA DO BANCO)
    // ───────────────────────────────────────────────

    /**
     * Calcula IRRF com base na tabela ativa vigente.
     */
    public function calcularIrrf(
        float $salarioBruto,
        float $inssDesconto,
        int $dependentes = 0,
        ?Carbon $dataReferencia = null
    ): array {
        $deducaoPorDependente = $this->getDeducaoDependenteIrrf($dataReferencia);
        $baseCalculo = $salarioBruto - $inssDesconto - ($dependentes * $deducaoPorDependente);

        if ($baseCalculo <= 0) {
            return [
                'base'            => 0,
                'valor'           => 0,
                'aliquota_efetiva' => 0,
            ];
        }

        $faixas = $this->getFaixasIrrfAtivas($dataReferencia);
        $valorIrrf = 0;

        // Busca a faixa onde a base de cálculo se encaixa
        foreach ($faixas as $faixa) {
            $teto = $faixa['teto'] ?? PHP_FLOAT_MAX;
            if ($baseCalculo >= ($faixa['minimo'] ?? 0) && $baseCalculo <= $teto) {
                $valorIrrf = ($baseCalculo * $faixa['aliquota']) - $faixa['deducao'];
                break;
            }
        }

        $valorIrrf       = max(0, round($valorIrrf, 2));
        $aliquotaEfetiva = $baseCalculo > 0
            ? round($valorIrrf / $baseCalculo, 4)
            : 0;

        return [
            'base'             => round($baseCalculo, 2),
            'valor'            => $valorIrrf,
            'aliquota_efetiva' => $aliquotaEfetiva,
        ];
    }

    /**
     * Retorna o resumo completo do IRRF para exibição (debug/info).
     */
    public function getResumoIrrf(
        float $salarioBruto,
        float $inssDesconto,
        int $dependentes = 0,
        ?Carbon $dataReferencia = null
    ): array {
        $deducaoPorDependente = $this->getDeducaoDependenteIrrf($dataReferencia);
        $baseCalculo = $salarioBruto - $inssDesconto - ($dependentes * $deducaoPorDependente);
        $faixas      = $this->getFaixasIrrfAtivas($dataReferencia);

        $faixaAplicada = null;
        $valorIrrf     = 0;

        foreach ($faixas as $faixa) {
            $teto = $faixa['teto'] ?? PHP_FLOAT_MAX;
            if ($baseCalculo >= ($faixa['minimo'] ?? 0) && $baseCalculo <= $teto) {
                $faixaAplicada = $faixa;
                $valorIrrf = ($baseCalculo * $faixa['aliquota']) - $faixa['deducao'];
                break;
            }
        }

        return [
            'salario_bruto'           => $salarioBruto,
            'inss'                    => $inssDesconto,
            'dependentes'             => $dependentes,
            'deducao_por_dependente'  => $deducaoPorDependente,
            'total_deducao_dep'       => $dependentes * $deducaoPorDependente,
            'base_calculo'            => round(max(0, $baseCalculo), 2),
            'faixa_aplicada'          => $faixaAplicada,
            'valor_irrf'              => round(max(0, $valorIrrf), 2),
            'aliquota_efetiva'        => $baseCalculo > 0 ? round(max(0, $valorIrrf) / $baseCalculo * 100, 2) : 0,
        ];
    }

    // ───────────────────────────────────────────────
    //  SALÁRIO FAMÍLIA
    // ───────────────────────────────────────────────

    /**
     * Calcula salário família (usa valor e limite da tabela INSS ativa).
     */
    public function calcularSalarioFamilia(Funcionario $funcionario, ?Carbon $dataReferencia = null): float
    {
        $dependentes = $funcionario->qtd_dependentes_salario_familia ?? 0;
        $salarioBase = $funcionario->salario_base ?? 0;

        if ($dependentes === 0) {
            return 0;
        }

        $tabela = $this->getTabelaInssAtiva($dataReferencia);

        if (!$tabela || $salarioBase > ($tabela->limite_salario_familia ?? 0)) {
            return 0;
        }

        return round($dependentes * ($tabela->valor_salario_familia ?? 0), 2);
    }
     // ═══════════════════════════════════════════════
    //  FGTS
    // ═══════════════════════════════════════════════

    public function calcularFGTS(
        Funcionario $funcionario,
        float $salarioBase,
        float $horasExtras = 0,
        float $dsrHE = 0
    ): float {
        if (!$this->deveAplicarFGTS($funcionario)) {
            return 0;
        }

        $baseFGTS = $salarioBase + $horasExtras + $dsrHE;
        return round($baseFGTS * self::PERCENTUAL_FGTS, 2);
    }

    // ───────────────────────────────────────────────
    //  VALE TRANSPORTE
    // ───────────────────────────────────────────────

    public function calcularValeTransporte(float $salarioBruto, float $valorTransporteReal = 0): float
    {
        if ($valorTransporteReal <= 0) {
            return 0;
        }

        $descontoMaximo = $salarioBruto * self::PERCENTUAL_VALE_TRANSPORTE;

        return round(min($descontoMaximo, $valorTransporteReal), 2);
    }

    // ───────────────────────────────────────────────
    //  DSR (DESCANSO SEMANAL REMUNERADO)
    // ───────────────────────────────────────────────

    public function calcularDSR(
        float $horasExtrasTotal,
        float $valorHoraExtra,
        Carbon $competencia,
        ?array $diasTrabalho = null
    ): float {
        if ($horasExtrasTotal == 0) {
            return 0;
        }

        $diasTrabalho = $diasTrabalho ?? self::DIAS_TRABALHO_PADRAO;
        $diasUteis        = $this->calcularDiasUteis($competencia, $diasTrabalho);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

        if ($diasUteis == 0) {
            return 0;
        }

        $mediaDiaria = $horasExtrasTotal / $diasUteis;

        return round($mediaDiaria * $domingosFeriados * $valorHoraExtra, 2);
    }

    public function calcularDSRFaltas(float $faltasValor, Carbon $competencia, ?array $diasTrabalho = null): float
    {
        if ($faltasValor == 0) {
            return 0;
        }

        $diasTrabalho = $diasTrabalho ?? self::DIAS_TRABALHO_PADRAO;
        $diasUteis        = $this->calcularDiasUteis($competencia, $diasTrabalho);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

        if ($diasUteis == 0) {
            return 0;
        }

        return round($faltasValor * ($domingosFeriados / $diasUteis), 2);
    }

    // ───────────────────────────────────────────────
    //  FALTAS
    // ───────────────────────────────────────────────

    public function calcularFaltasUsaDSR(float $faltasDias, float $salarioBase, Carbon $competencia, ?array $diasTrabalho = null): array
    {
        if ($faltasDias == 0) {
            return ['valor' => 0, 'dsr' => 0];
        }

        $diasTrabalho = $diasTrabalho ?? self::DIAS_TRABALHO_PADRAO;
        $diasUteis = $this->calcularDiasUteis($competencia, $diasTrabalho);

        if ($diasUteis == 0) {
            return ['valor' => 0, 'dsr' => 0];
        }

        $valorDia    = $salarioBase / $diasUteis;
        $valorFaltas = round($faltasDias * $valorDia, 2);
        $dsrFaltas   = $this->calcularDSRFaltas($valorFaltas, $competencia, $diasTrabalho);

        if($diasUteis == 0) {

            return ['valor' => 0, 'dsr' => 0];

        }

        $valorDia = $salarioBase / $diasUteis;
        $valorFaltas = round($faltasDias * $valorDia, 2);
        $dsrFaltas = $this->calcularDSRFaltas($valorFaltas, $competencia, $diasTrabalho);

        return [
            'valor' => $valorFaltas,
            'dsr'   => $dsrFaltas,
        ];
    }

    /**
     * Calcula valor das faltas.
     *
     * Regra de negócio:
     * - Valor do dia de falta: salário base ÷ 30 (mês comercial)
     * - DSR sobre faltas: (valor falta ÷ dias úteis) × domingos/feriados
     */
    public function calcularFaltas(
        float $faltasDias,
        float $salarioBase,
        Carbon $competencia,
        ?array $diasTrabalho = null
    ): array {
        if ($faltasDias == 0) {
            return ['valor' => 0, 'dsr' => 0, 'valor_dia' => 0];
        }

        // 🆕 Falta: SEMPRE usa 30 dias (mês comercial)
        $valorDia = $salarioBase / 30;
        $valorFaltas = round($faltasDias * $valorDia, 2);

        // DSR sobre faltas: usa os dias úteis da jornada do funcionário
        $diasTrabalho = $diasTrabalho ?? self::DIAS_TRABALHO_PADRAO;
        $diasUteis = $this->calcularDiasUteis($competencia, $diasTrabalho);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

        $dsrFaltas = 0;
        if ($diasUteis > 0 && $domingosFeriados > 0) {
            $dsrFaltas = round($valorFaltas * ($domingosFeriados / $diasUteis), 2);
        }

        return [
            'valor'     => $valorFaltas,
            'dsr'       => $dsrFaltas,
            'valor_dia' => round($valorDia, 2),
        ];
    }

    // ───────────────────────────────────────────────
    //  ARREDONDAMENTOS
    // ───────────────────────────────────────────────

    public function calcularArredondamentos(float $totalProventos, float $totalDescontos): array
    {
        $liquidoBruto    = $totalProventos - $totalDescontos;
        $liquidoTruncado = floor($liquidoBruto * 100) / 100;
        $diferenca       = round($liquidoBruto - $liquidoTruncado, 2);

        if ($diferenca > 0.005) {
            return ['provento' => 0, 'desconto' => round($diferenca, 2)];
        }

        if ($diferenca < -0.005) {
            return ['provento' => round(abs($diferenca), 2), 'desconto' => 0];
        }

        return ['provento' => 0, 'desconto' => 0];
    }

    // ───────────────────────────────────────────────
    //  CALENDÁRIO
    // ───────────────────────────────────────────────

    public function calcularDiasUteis(Carbon $competencia, array $diasTrabalho = []): int
    {

        if (empty($diasTrabalho)) {
            $diasTrabalho = self::DIAS_TRABALHO_PADRAO;
        }

        $uteis = 0;
        $data  = $competencia->copy()->startOfMonth();
        $fim   = $competencia->copy()->endOfMonth();

        // while ($data->lte($fim)) {
        //     if (!$data->isWeekend() && !$this->isFeriado($data)) {
        //         $uteis++;
        //     }
        //     $data->addDay();
        // }
        while ($data->lte($fim)) {
            if(in_array($data->dayOfWeek, $diasTrabalho) && !$this->isFeriado($data)) {
                $uteis++;
            }
            $data->addDay();
        }

        return $uteis;
    }

    public function calcularDomingosEFeriados(Carbon $competencia): int
    {
        $dsrs = 0;
        $data = $competencia->copy()->startOfMonth();
        $fim  = $competencia->copy()->endOfMonth();

        while ($data->lte($fim)) {
            if ($data->isSunday() || $this->isFeriado($data)) {
                $dsrs++;
            }
            $data->addDay();
        }

        Log::info('Dom/Fer detalhado:', [
            'mês' => $competencia->format('Y-m'),
            'inicio' => $data->format('d/m/Y'),
            'fim' => $fim->format('d/m/Y'),
            'total' => $dsrs,
        ]);

        return $dsrs;
    }

    public function calcularQuintoDiaUtil(Carbon $competencia, ?array $diasTrabalho = null): Carbon
    {
        // se for null ou vazio, usa o padrao
        if(empty($diasTrabalho)) {
            $diasTrabalho = self::DIAS_TRABALHO_PADRAO;
        }

        // $diasTrabalho = $diasTrabalho ?? self::DIAS_TRABALHO_PADRAO;
        $data  = $competencia->copy()->startOfMonth();
        $uteis = 0;

        // while ($uteis < 5) {
        //     if (!$data->isWeekend() && !$this->isFeriado($data)) {
        //         $uteis++;
        //     }
        //     if ($uteis < 5) {
        //         $data->addDay();
        //     }
        // }
        while ($uteis < 5) {
            if(in_array($data->dayOfWeek, $diasTrabalho) && !$this->isFeriado($data)) {
                $uteis++;
            }
            if ($uteis < 5) {
                $data->addDay();
            }
        }

        return $data;
    }

    public function getResumoCalendario(Carbon $competencia, ?array $diasTrabalho = []): array
    {
        return [
            'dias_uteis'        => $this->calcularDiasUteis($competencia, $diasTrabalho),
            'domingos_feriados' => $this->calcularDomingosEFeriados($competencia),
            'quinto_dia_util'   => $this->calcularQuintoDiaUtil($competencia, $diasTrabalho)->format('d/m/Y'),
            'dias_trabalho'     => $diasTrabalho,  // 🆕 Retorna quais dias foram usados
        ];
    }

    // Adicionar no CalculoTrabalhistaService:
    public function getValorSalarioFamilia(?Carbon $dataReferencia = null): float
    {
        $tabela = $this->getTabelaInssAtiva($dataReferencia);
        return (float) ($tabela->valor_salario_familia ?? 67.54);
    }

     // ═══════════════════════════════════════════════
    //  🆕 CÁLCULO COMPLETO DA FOLHA
    // ═══════════════════════════════════════════════

    /**
     * Calcula todos os valores da folha para um funcionário.
     * Usa os campos exatos da tabela folha_pagamentos.
     */
    public function calcularFolhaCompletaAnteior(
        Funcionario $funcionario,
        Carbon $competencia,
        float $horasExtras = 0,
        float $faltasDias = 0,
        float $valorTransporteReal = 0,
        float $adiantamentoSalarial = 0
    ): array {
        $contrato = $funcionario->contrato;
        if (!$contrato) {
            throw new \Exception("Funcionário #{$funcionario->id} não possui contrato.");
        }

        $diasTrabalho = $this->getDiasTrabalho($funcionario);
        $diasUteis = $this->calcularDiasUteis($competencia, $diasTrabalho);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);
        $quintoDiaUtil = $this->calcularQuintoDiaUtil($competencia, $diasTrabalho);

        // Valores base
        $salarioBase = (float) $contrato->salario_base;
        $valoresHora = $this->calcularValorHora($funcionario);

        // Horas extras
        if (!$this->podeFazerHoraExtra($funcionario)) {
            $horasExtras = 0;
        }
        $totalHorasExtras = round($horasExtras * $valoresHora['valor_hora_extra'], 2);

        // 🆕 DSR sobre horas extras - mesma fórmula do FolhaLancamentoService
        $dsrHE = 0;
        if ($totalHorasExtras > 0 && $diasUteis > 0) {
            $dsrHE = round(($totalHorasExtras / $diasUteis) * $domingosFeriados, 2);
        }

        // Salário família
        $salarioFamilia = $this->calcularSalarioFamilia($funcionario, $competencia);

        // Faltas (30 dias para valor do dia)
        $faltasValor = 0;
        if ($faltasDias > 0) {
            $faltasValor = round(($salarioBase / 30) * $faltasDias, 2);
        }

        // Proventos brutos (sem arredondamento)
        $totalProventosBruto = $salarioBase + $totalHorasExtras + $dsrHE + $salarioFamilia;

        // 🆕 Base INSS = Salário + HE + DSR - Faltas (sem salário família)
        $baseInss = round($salarioBase + $totalHorasExtras + $dsrHE - $faltasValor, 2);

        // INSS
        $inss = $this->deveAplicarINSS($funcionario)
            ? $this->calcularINSS($baseInss, $competencia)
            : 0;

        // IRRF
        if ($this->deveAplicarIRRF($funcionario)) {
            $dependentes = $contrato->qtd_dependentes_ir ?? 0;
            $irrf = $this->calcularIrrf($totalProventosBruto - $faltasValor, $inss, $dependentes, $competencia);
        } else {
            $irrf = ['base' => 0, 'valor' => 0, 'aliquota_efetiva' => 0];
        }

        // FGTS
        $baseFGTS = $totalProventosBruto - $faltasValor;
        $fgts = $this->calcularFGTS($funcionario, $baseFGTS, 0, 0);

        // Vale transporte
        $valorVT = $funcionario->beneficios->valor_vale_transporte ?? $valorTransporteReal;
        $valeTransporte = $this->calcularValeTransporte($totalProventosBruto, $valorVT);

        // Total descontos (sem arredondamento)
        $totalDescontosBruto = $inss
            + $irrf['valor']
            + $valeTransporte
            + $faltasValor
            + $adiantamentoSalarial;

        // 🆕 Arredondamento - mesma lógica do frontend
        $liquidoBruto = $totalProventosBruto - $totalDescontosBruto;
        $arredondamentoProvento = 0;
        $arredondamentoDesconto = 0;
        if ($liquidoBruto != floor($liquidoBruto)) {
            $liquidoArredondado = ceil($liquidoBruto);
            $arredondamentoProvento = round($liquidoArredondado - $liquidoBruto, 2);
        }

        // Totais finais
        $totalProventos = $totalProventosBruto + $arredondamentoProvento;
        $totalDescontos = $totalDescontosBruto + $arredondamentoDesconto;
        $liquido = $totalProventos - $totalDescontos;

        return [
            'competencia'              => $competencia->format('Y-m'),
            'funcionario_id'           => $funcionario->id,
            'nome'                     => $funcionario->nome_completo,
            'tipo_contrato'            => $contrato->tipo_contrato,
            'dias_trabalho'            => $diasTrabalho,
            'dias_uteis'               => $diasUteis,
            'domingos_feriados'        => $domingosFeriados,
            'quinto_dia_util'          => $quintoDiaUtil->format('Y-m-d'),

            // Proventos
            'salario_base'             => $salarioBase,
            'valor_hora_normal'        => $valoresHora['valor_hora_normal'],
            'valor_hora_extra'         => $valoresHora['valor_hora_extra'],
            'horas_extras_totais'      => $horasExtras,
            'total_horas_extras'       => $totalHorasExtras,
            'dsr_he'                   => $dsrHE,
            'salario_familia'          => $salarioFamilia,
            'total_proventos'          => round($totalProventos, 2),

            // Descontos
            'faltas_dias'              => $faltasDias,
            'faltas_valor'             => $faltasValor,
            'inss'                     => $inss,
            'inss_aliquota'            => $baseInss > 0 ? round(($inss / $baseInss) * 100, 4) : 0,
            'irrf_valor'               => $irrf['valor'],
            'irrf_base'                => $irrf['base'],
            'fgts_valor'               => $fgts,
            'fgts_base'                => $baseFGTS,
            'vale_transporte'          => $valeTransporte,
            'adiantamento_salarial'    => $adiantamentoSalarial,
            'total_descontos'          => round($totalDescontos, 2),

            // Finais
            'arredondamento_provento'  => $arredondamentoProvento,
            'arredondamento_desconto'  => $arredondamentoDesconto,
            'salario_liquido'          => round($liquido, 2),

            // Bases
            'base_inss'                => $baseInss,
            'base_fgts'                => round($baseFGTS, 2),
            'base_irrf'                => round($irrf['base'], 2),
        ];
    }

    public function calcularFolhaCompleta(
        Funcionario $funcionario,
        Carbon $competencia,
        float $horasExtras = 0,
        float $faltasDias = 0,
        float $valorTransporteReal = 0,
        float $adiantamentoSalarial = 0  // 🆕
    ): array {
        $contrato = $funcionario->contrato;
        if (!$contrato) {
            throw new \Exception("Funcionário #{$funcionario->id} não possui contrato.");
        }

        $diasTrabalho = $this->getDiasTrabalho($funcionario);
        $diasUteis = $this->calcularDiasUteis($competencia, $diasTrabalho);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);
        $quintoDiaUtil = $this->calcularQuintoDiaUtil($competencia, $diasTrabalho);

        // Valores base
        $salarioBase = $contrato->salario_base;
        $valoresHora = $this->calcularValorHora($funcionario);

        // Horas extras
        if (!$this->podeFazerHoraExtra($funcionario)) {
            $horasExtras = 0;
        }
        $totalHorasExtras = round($horasExtras * $valoresHora['valor_hora_extra'], 2);

        // DSR sobre horas extras
        $dsrHE = $this->calcularDSR($horasExtras, $valoresHora['valor_hora_extra'], $competencia, $diasTrabalho);

        // Salário família
        $salarioFamilia = $this->calcularSalarioFamilia($funcionario, $competencia);

        // 🆕 Faltas (30 dias para valor do dia)
        $calculoFaltas = $this->calcularFaltas($faltasDias, $salarioBase, $competencia, $diasTrabalho);

        // Proventos (antes de descontos)
        $totalProventosBruto = $salarioBase + $totalHorasExtras + $dsrHE + $salarioFamilia;

        // INSS
        $inss = $this->deveAplicarINSS($funcionario)
            ? $this->calcularINSS($totalProventosBruto - $calculoFaltas['valor'], $competencia)
            : 0;

        // IRRF
        if ($this->deveAplicarIRRF($funcionario)) {
            $dependentes = $contrato->qtd_dependentes_ir ?? 0;
            $irrf = $this->calcularIrrf($totalProventosBruto - $calculoFaltas['valor'], $inss, $dependentes, $competencia);
        } else {
            $irrf = ['base' => 0, 'valor' => 0, 'aliquota_efetiva' => 0];
        }

        // FGTS
        $baseFGTS = $totalProventosBruto - $calculoFaltas['valor'];
        $fgts = $this->calcularFGTS($funcionario, $baseFGTS, 0, 0);

        // Vale transporte
        $valorVT = $funcionario->beneficios->valor_vale_transporte ?? $valorTransporteReal;
        $valeTransporte = $this->calcularValeTransporte($totalProventosBruto, $valorVT);

        // Total descontos
        $totalDescontos = $inss
            + $irrf['valor']
            + $valeTransporte
            + $calculoFaltas['valor']
            + $calculoFaltas['dsr']
            + $adiantamentoSalarial;

        // Arredondamentos
        $arredondamentos = $this->calcularArredondamentos($totalProventosBruto, $totalDescontos);

        // Líquido
        $liquido = $totalProventosBruto
            + $arredondamentos['provento']
            - $totalDescontos
            - $arredondamentos['desconto'];

        return [
            'competencia'              => $competencia->format('Y-m'),
            'funcionario_id'           => $funcionario->id,
            'nome'                     => $funcionario->nome_completo,
            'tipo_contrato'            => $contrato->tipo_contrato,
            'dias_trabalho'            => $diasTrabalho,
            'dias_uteis'               => $diasUteis,
            'domingos_feriados'        => $domingosFeriados,
            'quinto_dia_util'          => $quintoDiaUtil->format('Y-m-d'),

            // Proventos
            'salario_base'             => round($salarioBase, 2),
            'valor_hora_normal'        => $valoresHora['valor_hora_normal'],
            'valor_hora_extra'         => $valoresHora['valor_hora_extra'],
            'horas_extras_totais'      => $horasExtras,
            'total_horas_extras'       => $totalHorasExtras,
            'dsr_he'                   => $dsrHE,
            'salario_familia'          => $salarioFamilia,
            'total_proventos'          => round($totalProventosBruto + $arredondamentos['provento'], 2),

            // Descontos
            'faltas_dias'              => $faltasDias,
            'faltas_valor'             => $calculoFaltas['valor'],
            'faltas_valor_dia'         => $calculoFaltas['valor_dia'],
            'faltas_dsr'               => $calculoFaltas['dsr'],
            'inss'                     => $inss,
            'inss_aliquota'            => $this->getAliquotaEfetivaINSS($totalProventosBruto - $calculoFaltas['valor'], $competencia),
            'irrf_valor'               => $irrf['valor'],
            'irrf_base'                => $irrf['base'],
            'fgts_valor'               => $fgts,
            'fgts_base'                => $baseFGTS,
            'vale_transporte'          => $valeTransporte,
            'adiantamento_salarial'    => $adiantamentoSalarial,
            'total_descontos'          => round($totalDescontos + $arredondamentos['desconto'], 2),

            // Finais
            'arredondamento_provento'  => $arredondamentos['provento'],
            'arredondamento_desconto'  => $arredondamentos['desconto'],
            'salario_liquido'          => round($liquido, 2),

            // Bases
            'base_inss'                => round($totalProventosBruto - $calculoFaltas['valor'], 2),
            'base_fgts'                => round($baseFGTS, 2),
            'base_irrf'                => round($irrf['base'], 2),
        ];
    }


    // ───────────────────────────────────────────────
    //  FERIADOS
    // ───────────────────────────────────────────────

    public function isFeriado(Carbon $data): bool
    {
        // Fixos
        if (in_array($data->format('d-m'), self::FERIADOS_FIXOS)) {
            return true;
        }

        // Móveis (baseados na Páscoa)
        $pascoa = $this->calcularPascoa($data->year);
        $feriadosMoveis = [
            $pascoa->copy()->subDays(48)->format('Y-m-d'), // Segunda Carnaval
            $pascoa->copy()->subDays(47)->format('Y-m-d'), // Terça Carnaval
            $pascoa->copy()->subDays(2)->format('Y-m-d'),  // Sexta-feira Santa
            // $pascoa->copy()->addDays(60)->format('Y-m-d'), // Corpus Christi
        ];

        return in_array($data->format('Y-m-d'), $feriadosMoveis);
    }

    private function calcularPascoa(int $ano): Carbon
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv(($b + 8), 25);
        $g = intdiv(($b - $f + 1), 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv(($a + 11 * $h + 22 * $l), 451);
        $mes = intdiv(($h + $l - 7 * $m + 114), 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($ano, $mes, $dia);
    }

    // ═══════════════════════════════════════════════
    //  🔌 CONSULTAS AO BANCO (TABELAS DINÂMICAS)
    // ═══════════════════════════════════════════════

    /**
     * Busca as faixas de INSS da tabela ativa vigente.
     * Usa cache para evitar N consultas na mesma requisição.
     */
    private function getFaixasInssAtivas(?Carbon $dataReferencia = null): array
    {
        if ($this->faixasInssCache !== null) {
            return $this->faixasInssCache;
        }

        $data = $dataReferencia ?? now();

        $cacheKey = 'faixas_inss_ativas_' . $data->format('Y_m');

        // $this->faixasInssCache = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data) {
        //     $tabela = TabelaInss::with('faixas')
        //         ->where('ativo', true)
        //         ->where('vigencia_inicio', '<=', $data)
        //         ->where(function ($q) use ($data) {
        //             $q->whereNull('vigencia_fim')
        //               ->orWhere('vigencia_fim', '>=', $data);
        //         })
        //         ->orderBy('ano_vigencia', 'desc')
        //         ->first();

        //     if (!$tabela) {
        //         return $this->getFaixasInssFallback();
        //     }

        //     return $tabela->faixas
        //         ->sortBy('ordem')
        //         ->map(fn ($f) => [
        //             'ordem'    => $f->ordem,
        //             'teto'     => (float) $f->teto,
        //             'aliquota' => (float) $f->aliquota,
        //         ])
        //         ->values()
        //         ->toArray();
        // });
         $this->faixasInssCache = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data) {
            $tabela = TabelaInss::with('faixas')
                ->where('ativo', true)
                ->where('vigencia_inicio', '<=', $data)
                ->where(function ($q) use ($data) {
                    $q->whereNull('vigencia_fim')->orWhere('vigencia_fim', '>=', $data);
                })
                ->orderBy('ano_vigencia', 'desc')
                ->first();

            if (!$tabela) return $this->getFaixasInssFallback();

            return $tabela->faixas->sortBy('ordem')->map(fn($f) => [
                'ordem'    => $f->ordem,
                'teto'     => (float) $f->teto,
                'aliquota' => (float) $f->aliquota,
                'deducao'  => (float) ($f->deducao ?? 0),  // 🆕 ADICIONE ESTA LINHA
            ])->values()->toArray();
        });

    

        return $this->faixasInssCache;
    }

    /**
     * Busca as faixas de IRRF da tabela ativa vigente.
     */
    private function getFaixasIrrfAtivas(?Carbon $dataReferencia = null): array
    {
        if ($this->faixasIrrfCache !== null) {
            return $this->faixasIrrfCache;
        }

        $data = $dataReferencia ?? now();

        $cacheKey = 'faixas_irrf_ativas_' . $data->format('Y_m');

        $this->faixasIrrfCache = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data) {
            $tabela = TabelaIrrf::with('faixas')
                ->where('ativo', true)
                ->where('vigencia_inicio', '<=', $data)
                ->where(function ($q) use ($data) {
                    $q->whereNull('vigencia_fim')
                      ->orWhere('vigencia_fim', '>=', $data);
                })
                ->orderBy('ano_vigencia', 'desc')
                ->first();

            if (!$tabela) {
                return $this->getFaixasIrrfFallback();
            }

            return $tabela->faixas
                ->sortBy('ordem')
                ->map(fn ($f) => [
                    'ordem'    => $f->ordem,
                    'minimo'   => (float) $f->minimo,
                    'teto'     => (float) $f->teto,
                    'aliquota' => (float) $f->aliquota,
                    'deducao'  => (float) $f->deducao,
                ])
                ->values()
                ->toArray();
        });

        return $this->faixasIrrfCache;
    }

    /**
     * Busca o valor de dedução por dependente da tabela IRRF ativa.
     */
    private function getDeducaoDependenteIrrf(?Carbon $dataReferencia = null): float
    {
        if ($this->deducaoDependenteIrrfCache !== null) {
            return $this->deducaoDependenteIrrfCache;
        }

        $data = $dataReferencia ?? now();

        $cacheKey = 'deducao_dependente_irrf_' . $data->format('Y_m');

        $this->deducaoDependenteIrrfCache = Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($data) {
                $tabela = TabelaIrrf::where('ativo', true)
                    ->where('vigencia_inicio', '<=', $data)
                    ->where(function ($q) use ($data) {
                        $q->whereNull('vigencia_fim')
                          ->orWhere('vigencia_fim', '>=', $data);
                    })
                    ->orderBy('ano_vigencia', 'desc')
                    ->first();

                return (float) ($tabela->deducao_dependente ?? 189.59);
            }
        );

        return $this->deducaoDependenteIrrfCache;
    }

    /**
     * Busca a tabela INSS ativa.
     * Retorna uma instância limpa para evitar problemas de desserialização em cache.
     */
    private function getTabelaInssAtiva(?Carbon $dataReferencia = null): ?TabelaInss
    {
        $data = $dataReferencia ?? now();
        $cacheKey = 'tabela_inss_ativa_array_' . $data->format('Y_m');

        // 1. Guardamos apenas os atributos necessários em formato de array no cache
        $dadosTabela = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data) {
            $tabela = TabelaInss::query()
                ->where('ativo', true)
                ->whereDate('vigencia_inicio', '<=', $data)
                ->where(function ($query) use ($data) {
                    $query->whereNull('vigencia_fim')
                        ->orWhereDate('vigencia_fim', '>=', $data);
                })
                ->orderBy('ano_vigencia', 'desc')
                ->first();

            return $tabela ? $tabela->toArray() : null;
        });

        if (!$dadosTabela) {
            return null;
        }

        // 2. Reconstruímos uma model "viva" e limpa a partir do array guardado
        // Isso evita o erro de classe incompleta independente do driver de cache!
        return (new TabelaInss())->newFromBuilder($dadosTabela);
    }
    // ───────────────────────────────────────────────
    //  FALLBACKS (se não houver tabela no banco)
    // ───────────────────────────────────────────────

    private function getFaixasInssFallback(): array
    {
        return [
            ['ordem' => 1, 'teto' => 1412.00, 'aliquota' => 0.075],
            ['ordem' => 2, 'teto' => 2666.68, 'aliquota' => 0.09],
            ['ordem' => 3, 'teto' => 4000.03, 'aliquota' => 0.12],
            ['ordem' => 4, 'teto' => 7786.02, 'aliquota' => 0.14],
        ];
    }

    private function getFaixasIrrfFallback(): array
    {
        return [
            ['ordem' => 1, 'minimo' => 0,       'teto' => 2259.20, 'aliquota' => 0,    'deducao' => 0],
            ['ordem' => 2, 'minimo' => 2259.21, 'teto' => 2826.65, 'aliquota' => 0.075, 'deducao' => 169.44],
            ['ordem' => 3, 'minimo' => 2826.66, 'teto' => 3751.05, 'aliquota' => 0.15,  'deducao' => 381.44],
            ['ordem' => 4, 'minimo' => 3751.06, 'teto' => 4664.68, 'aliquota' => 0.225, 'deducao' => 662.77],
            ['ordem' => 5, 'minimo' => 4664.69, 'teto' => PHP_FLOAT_MAX, 'aliquota' => 0.275, 'deducao' => 896.00],
        ];
    }
}