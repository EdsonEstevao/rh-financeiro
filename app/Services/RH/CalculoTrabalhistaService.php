<?php

namespace App\Services\RH;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

use App\Models\Domain\RH\{Funcionario, TabelaInss, TabelaIrrf};

class CalculoTrabalhistaService
{
    // Propriedades de cache em memória (para não bater no banco repetidamente)
    private ?array $faixasInssCache            = null;
    private ?array $faixasIrrfCache             = null;
    private ?float $deducaoDependenteIrrfCache = null;
    private ?TabelaInss $tabelaInssCache      = null;
    // ─── CONSTANTES DE REGRA DE NEGÓCIO ────────────
    // (não mudam por tabela — são definidas por lei/CLT)

    private const HORAS_MENSAIS_PADRAO        = 220;  // 44h semanais
    private const PERCENTUAL_HORA_EXTRA       = 0.50; // 50%
    private const PERCENTUAL_HORA_FERIADO     = 1.00; // 100%
    private const PERCENTUAL_VALE_TRANSPORTE  = 0.06; // 6%

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

    // ───────────────────────────────────────────────
    //  JORNADA DE TRABALHO
    // ───────────────────────────────────────────────

    public function calcularValorHora(Funcionario $funcionario): array
    {
        $salarioBase  = $funcionario->salario_base ?? 0;
        $cargaHoraria = $funcionario->carga_horaria_semanal ?? 44;
        $horasMensais = ($cargaHoraria / 6) * 30;

        $valorHoraNormal  = $horasMensais > 0 ? $salarioBase / $horasMensais : 0;
        $valorHoraExtra   = $valorHoraNormal * (1 + self::PERCENTUAL_HORA_EXTRA);
        $valorHoraFeriado = $valorHoraNormal * (1 + self::PERCENTUAL_HORA_FERIADO);

        return [
            'horas_mensais'      => round($horasMensais, 2),
            'valor_hora_normal'  => round($valorHoraNormal, 2),
            'valor_hora_extra'   => round($valorHoraExtra, 2),
            'valor_hora_feriado' => round($valorHoraFeriado, 2),
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

        if (empty($faixas)) {
            return 0;
        }

        $inss     = 0;
        $restante = $salarioBase;
        $anterior = 0;

        foreach ($faixas as $faixa) {
            $tetoFaixa  = $faixa['teto'] ?? PHP_FLOAT_MAX;
            $aliquota   = $faixa['aliquota'];
            $valorFaixa = min($restante, $tetoFaixa - $anterior);

            if ($valorFaixa <= 0) {
                break;
            }

            $inss     += $valorFaixa * $aliquota;
            $restante -= $valorFaixa;
            $anterior  = $tetoFaixa;

            if ($restante <= 0) {
                break;
            }
        }

        return round($inss, 2);
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
        Carbon $competencia
    ): float {
        if ($horasExtrasTotal == 0) {
            return 0;
        }

        $diasUteis        = $this->calcularDiasUteis($competencia);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

        if ($diasUteis == 0) {
            return 0;
        }

        $mediaDiaria = $horasExtrasTotal / $diasUteis;

        return round($mediaDiaria * $domingosFeriados * $valorHoraExtra, 2);
    }

    public function calcularDSRFaltas(float $faltasValor, Carbon $competencia): float
    {
        if ($faltasValor == 0) {
            return 0;
        }

        $diasUteis        = $this->calcularDiasUteis($competencia);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

        if ($diasUteis == 0) {
            return 0;
        }

        return round($faltasValor * ($domingosFeriados / $diasUteis), 2);
    }

    // ───────────────────────────────────────────────
    //  FALTAS
    // ───────────────────────────────────────────────

    public function calcularFaltas(float $faltasDias, float $salarioBase, Carbon $competencia): array
    {
        if ($faltasDias == 0) {
            return ['valor' => 0, 'dsr' => 0];
        }

        $diasUteis = $this->calcularDiasUteis($competencia);

        if ($diasUteis == 0) {
            return ['valor' => 0, 'dsr' => 0];
        }

        $valorDia    = $salarioBase / $diasUteis;
        $valorFaltas = round($faltasDias * $valorDia, 2);
        $dsrFaltas   = $this->calcularDSRFaltas($valorFaltas, $competencia);

        return [
            'valor' => $valorFaltas,
            'dsr'   => $dsrFaltas,
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

    public function calcularDiasUteis(Carbon $competencia): int
    {
        $uteis = 0;
        $data  = $competencia->copy()->startOfMonth();
        $fim   = $competencia->copy()->endOfMonth();

        while ($data->lte($fim)) {
            if (!$data->isWeekend() && !$this->isFeriado($data)) {
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

        return $dsrs;
    }

    public function calcularQuintoDiaUtil(Carbon $competencia): Carbon
    {
        $data  = $competencia->copy()->startOfMonth();
        $uteis = 0;

        while ($uteis < 5) {
            if (!$data->isWeekend() && !$this->isFeriado($data)) {
                $uteis++;
            }
            if ($uteis < 5) {
                $data->addDay();
            }
        }

        return $data;
    }

    public function getResumoCalendario(Carbon $competencia): array
    {
        return [
            'dias_uteis'        => $this->calcularDiasUteis($competencia),
            'domingos_feriados' => $this->calcularDomingosEFeriados($competencia),
            'quinto_dia_util'   => $this->calcularQuintoDiaUtil($competencia)->format('d/m/Y'),
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
            $pascoa->copy()->addDays(60)->format('Y-m-d'), // Corpus Christi
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

        $this->faixasInssCache = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($data) {
            $tabela = TabelaInss::with('faixas')
                ->where('ativo', true)
                ->where('vigencia_inicio', '<=', $data)
                ->where(function ($q) use ($data) {
                    $q->whereNull('vigencia_fim')
                      ->orWhere('vigencia_fim', '>=', $data);
                })
                ->orderBy('ano_vigencia', 'desc')
                ->first();

            if (!$tabela) {
                return $this->getFaixasInssFallback();
            }

            return $tabela->faixas
                ->sortBy('ordem')
                ->map(fn ($f) => [
                    'ordem'    => $f->ordem,
                    'teto'     => (float) $f->teto,
                    'aliquota' => (float) $f->aliquota,
                ])
                ->values()
                ->toArray();
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
     * Busca a tabela INSS ativa (model completo, usado pelo salário família).
     */
    private function getTabelaInssAtiva(?Carbon $dataReferencia = null): ?TabelaInss
    {
        $data = $dataReferencia ?? now();

        return Cache::remember(
            'tabela_inss_ativa_' . $data->format('Y_m'),
            self::CACHE_TTL,
            function () use ($data) {
                return TabelaInss::where('ativo', true)
                    ->where('vigencia_inicio', '<=', $data)
                    ->where(function ($q) use ($data) {
                        $q->whereNull('vigencia_fim')
                          ->orWhere('vigencia_fim', '>=', $data);
                    })
                    ->orderBy('ano_vigencia', 'desc')
                    ->first();
            }
        );
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