<?php

namespace App\Services\RH;

class CalculoTrabalhistaService
{
    /**
     * Calcula INSS baseado na tabela 2026 (valores exemplo)
     * Você pode parametrizar isso em uma tabela depois
     */
    public function calcularInss(float $salarioBruto): array
    {
        // Tabela INSS 2026 (valores exemplo - ajustar conforme legislação)
        $faixas = [
            ['min' => 0,       'max' => 1412.00, 'aliquota' => 0.075],
            ['min' => 1412.01, 'max' => 2666.68, 'aliquota' => 0.09],
            ['min' => 2666.69, 'max' => 4000.03, 'aliquota' => 0.12],
            ['min' => 4000.04, 'max' => 7786.02, 'aliquota' => 0.14],
        ];

        $teto = 7786.02; // Teto INSS 2026 (exemplo)
        $baseCalculo = min($salarioBruto, $teto);
        $valorInss = 0;
        $aliquotaEfetiva = 0;

        foreach ($faixas as $faixa) {
            if ($baseCalculo > $faixa['min']) {
                $valorFaixa = min($baseCalculo, $faixa['max']) - $faixa['min'];
                $valorInss += $valorFaixa * $faixa['aliquota'];
            }
        }

        if ($baseCalculo > 0) {
            $aliquotaEfetiva = $valorInss / $baseCalculo;
        }

        return [
            'base' => $baseCalculo,
            'valor' => round($valorInss, 2),
            'aliquota_efetiva' => round($aliquotaEfetiva, 4),
        ];
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