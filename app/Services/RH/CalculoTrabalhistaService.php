<?php

namespace App\Services\RH;

use Illuminate\Support\Carbon;

use App\Models\Domain\RH\Funcionario;

class CalculoTrabalhistaService
{
     // ─── CONSTANTES ────────────────────────────────

    private const HORAS_MENSAIS_PADRAO = 220; // 44h semanais / 6 dias * 30
    private const PERCENTUAL_HORA_EXTRA = 0.50; // 50%
    private const PERCENTUAL_HORA_FERIADO = 1.00; // 100%

    // Tabela INSS 2024 (progressiva)
    private const FAIXAS_INSS = [
        ['limite' => 1412.00, 'aliquota' => 0.075],
        ['limite' => 2666.68, 'aliquota' => 0.09],
        ['limite' => 4000.03, 'aliquota' => 0.12],
        ['limite' => 7786.02, 'aliquota' => 0.14],
    ];

    private const VALOR_SALARIO_FAMILIA = 62.04;
    private const LIMITE_SALARIO_FAMILIA = 1819.26;

    // Feriados nacionais fixos
    private const FERIADOS_FIXOS = [
        '01-01', '21-04', '01-05', '07-09',
        '12-10', '02-11', '15-11', '25-12',
    ];

    // ─── JORNADA DE TRABALHO ───────────────────────

    /**
     * Calcula valor da hora normal e extra
     */
    public function calcularValorHora(Funcionario $funcionario): array
    {
        $salarioBase = $funcionario->salario_base ?? 0;
        $cargaHoraria = $funcionario->carga_horaria_semanal ?? 44;
        $horasMensais = ($cargaHoraria / 6) * 30;

        $valorHoraNormal = $horasMensais > 0 ? $salarioBase / $horasMensais : 0;
        $valorHoraExtra = $valorHoraNormal * (1 + self::PERCENTUAL_HORA_EXTRA);
        $valorHoraFeriado = $valorHoraNormal * (1 + self::PERCENTUAL_HORA_FERIADO);

        return [
            'horas_mensais' => round($horasMensais, 2),
            'valor_hora_normal' => round($valorHoraNormal, 2),
            'valor_hora_extra' => round($valorHoraExtra, 2),
            'valor_hora_feriado' => round($valorHoraFeriado, 2),
        ];
    }

    // ─── INSS ───────────────────────────────────────

    /**
     * Calcula INSS progressivo
     */
    public function calcularINSS(float $salarioBase): float
    {
        $inss = 0;
        $restante = $salarioBase;

        foreach (self::FAIXAS_INSS as $faixa) {
            $valorFaixa = min($restante, $faixa['limite']);
            $inss += $valorFaixa * $faixa['aliquota'];
            $restante -= $valorFaixa;

            if ($restante <= 0) break;
        }

        return round($inss, 2);
    }

    /**
     * Retorna a alíquota efetiva do INSS
     */
    public function getAliquotaEfetivaINSS(float $salarioBase): float
    {
        $inss = $this->calcularINSS($salarioBase);
        return $salarioBase > 0 ? round(($inss / $salarioBase) * 100, 2) : 0;
    }

    // ─── SALÁRIO FAMÍLIA ────────────────────────────

    /**
     * Calcula salário família baseado nos dependentes
     */
    public function calcularSalarioFamilia(Funcionario $funcionario): float
    {
        $dependentes = $funcionario->qtd_dependentes_salario_familia ?? 0;
        $salarioBase = $funcionario->salario_base ?? 0;

        if ($dependentes === 0 || $salarioBase > self::LIMITE_SALARIO_FAMILIA) {
            return 0;
        }

        return round($dependentes * self::VALOR_SALARIO_FAMILIA, 2);
    }

    // ─── DSR (DESCANSO SEMANAL REMUNERADO) ──────────

    /**
     * Calcula DSR sobre horas extras
     */
    public function calcularDSR(
        float $horasExtrasTotal,
        float $valorHoraExtra,
        Carbon $competencia
    ): float {
        if ($horasExtrasTotal == 0) {
            return 0;
        }

        $diasUteis = $this->calcularDiasUteis($competencia);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

        if ($diasUteis == 0) {
            return 0;
        }

        $mediaDiaria = $horasExtrasTotal / $diasUteis;

        return round($mediaDiaria * $domingosFeriados * $valorHoraExtra, 2);
    }

    /**
     * Calcula DSR sobre faltas
     */
    public function calcularDSRFaltas(float $faltasValor, Carbon $competencia): float
    {
        if ($faltasValor == 0) {
            return 0;
        }

        $diasUteis = $this->calcularDiasUteis($competencia);
        $domingosFeriados = $this->calcularDomingosEFeriados($competencia);

        if ($diasUteis == 0) {
            return 0;
        }

        return round($faltasValor * ($domingosFeriados / $diasUteis), 2);
    }

    // ─── FALTAS ─────────────────────────────────────

    /**
     * Calcula valor das faltas (dias não trabalhados)
     */
    public function calcularFaltas(float $faltasDias, float $salarioBase, Carbon $competencia): array
    {
        if ($faltasDias == 0) {
            return ['valor' => 0, 'dsr' => 0];
        }

        $diasUteis = $this->calcularDiasUteis($competencia);

        if ($diasUteis == 0) {
            return ['valor' => 0, 'dsr' => 0];
        }

        $valorDia = $salarioBase / $diasUteis;
        $valorFaltas = round($faltasDias * $valorDia, 2);
        $dsrFaltas = $this->calcularDSRFaltas($valorFaltas, $competencia);

        return [
            'valor' => $valorFaltas,
            'dsr' => $dsrFaltas,
        ];
    }

    // ─── ARREDONDAMENTOS ────────────────────────────

    /**
     * Calcula arredondamentos para fechar centavos
     */
    public function calcularArredondamentos(float $totalProventos, float $totalDescontos): array
    {
        $liquidoBruto = $totalProventos - $totalDescontos;
        $liquidoTruncado = floor($liquidoBruto * 100) / 100;
        $diferenca = round($liquidoBruto - $liquidoTruncado, 2);

        if ($diferenca > 0.005) {
            return ['provento' => 0, 'desconto' => round($diferenca, 2)];
        } elseif ($diferenca < -0.005) {
            return ['provento' => round(abs($diferenca), 2), 'desconto' => 0];
        }

        return ['provento' => 0, 'desconto' => 0];
    }

    // ─── CALENDÁRIO ─────────────────────────────────

    /**
     * Calcula dias úteis do mês
     */
    public function calcularDiasUteis(Carbon $competencia): int
    {
        $uteis = 0;
        $data = $competencia->copy()->startOfMonth();
        $fim = $competencia->copy()->endOfMonth();

        while ($data->lte($fim)) {
            if (!$data->isWeekend() && !$this->isFeriado($data)) {
                $uteis++;
            }
            $data->addDay();
        }

        return $uteis;
    }

    /**
     * Calcula domingos e feriados do mês
     */
    public function calcularDomingosEFeriados(Carbon $competencia): int
    {
        $dsrs = 0;
        $data = $competencia->copy()->startOfMonth();
        $fim = $competencia->copy()->endOfMonth();

        while ($data->lte($fim)) {
            if ($data->isSunday() || $this->isFeriado($data)) {
                $dsrs++;
            }
            $data->addDay();
        }

        return $dsrs;
    }

    /**
     * Calcula o 5º dia útil do mês
     */
    public function calcularQuintoDiaUtil(Carbon $competencia): Carbon
    {
        $data = $competencia->copy()->startOfMonth();
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

    /**
     * Retorna resumo do calendário do mês
     */
    public function getResumoCalendario(Carbon $competencia): array
    {
        return [
            'dias_uteis' => $this->calcularDiasUteis($competencia),
            'domingos_feriados' => $this->calcularDomingosEFeriados($competencia),
            'quinto_dia_util' => $this->calcularQuintoDiaUtil($competencia)->format('d/m/Y'),
        ];
    }

    // ─── FERIADOS ───────────────────────────────────

    /**
     * Verifica se uma data é feriado
     */
    public function isFeriado(Carbon $data): bool
    {
        // Feriados fixos
        if (in_array($data->format('d-m'), self::FERIADOS_FIXOS)) {
            return true;
        }

        // Feriados móveis
        $pascoa = $this->calcularPascoa($data->year);
        $feriadosMoveis = [
            $pascoa->copy()->subDays(48)->format('Y-m-d'), // Segunda Carnaval
            $pascoa->copy()->subDays(47)->format('Y-m-d'), // Terça Carnaval
            $pascoa->copy()->subDays(2)->format('Y-m-d'),  // Sexta-feira Santa
            $pascoa->copy()->addDays(60)->format('Y-m-d'), // Corpus Christi
        ];

        return in_array($data->format('Y-m-d'), $feriadosMoveis);
    }

    /**
     * Calcula data da Páscoa (Algoritmo de Meeus)
     */
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

    /**
     * Calcula IRRF baseado na tabela 2026 (valores exemplo)
     */
    public function calcularIrrf(float $salarioBruto, float $inssDesconto, int $dependentes = 0): array
    {
        $deducaoporDependente = 189.59; // 2026 exemplo
        $baseCalculo = $salarioBruto - $inssDesconto - ($dependentes * $deducaoporDependente);

        if ($baseCalculo <= 0) {
            return ['base' => 0, 'valor' => 0, 'aliquota_efetiva' => 0];
        }

        // Tabela IRRF 2026 (valores exemplo)
        $faixas = [
            ['min' => 0,       'max' => 2259.20, 'aliquota' => 0,    'deducao' => 0],
            ['min' => 2259.21, 'max' => 2826.65, 'aliquota' => 0.075, 'deducao' => 169.44],
            ['min' => 2826.66, 'max' => 3751.05, 'aliquota' => 0.15,  'deducao' => 381.44],
            ['min' => 3751.06, 'max' => 4664.68, 'aliquota' => 0.225, 'deducao' => 662.77],
            ['min' => 4664.69, 'max' => PHP_FLOAT_MAX, 'aliquota' => 0.275, 'deducao' => 896.00],
        ];

        $valorIrrf = 0;

        foreach ($faixas as $faixa) {
            if ($baseCalculo >= $faixa['min'] && $baseCalculo <= $faixa['max']) {
                $valorIrrf = ($baseCalculo * $faixa['aliquota']) - $faixa['deducao'];
                break;
            }
        }

        $valorIrrf = max(0, $valorIrrf); // Nunca negativo
        $aliquotaEfetiva = $baseCalculo > 0 ? $valorIrrf / $baseCalculo : 0;

        return [
            'base' => $baseCalculo,
            'valor' => round($valorIrrf, 2),
            'aliquota_efetiva' => round($aliquotaEfetiva, 4),
        ];
    }

    /**
     * Calcula Vale Transporte (6% do salário, limitado ao valor real)
     */
    public function calcularValeTransporte(float $salarioBruto, float $valorTransporteReal = 0): float
    {
        if ($valorTransporteReal <= 0) {
            return 0; // Funcionário não usa transporte público
        }

        $descontoMaximo = $salarioBruto * 0.06; // 6% do salário
        return min($descontoMaximo, $valorTransporteReal);
    }
}
