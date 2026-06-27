<?php

namespace App\Services\RH;



use App\Models\Domain\RH\{TabelaInss, TabelaIrrf};



class CalculoTributarioService
{
    /**
     * Calcula INSS com base na tabela vigente.
     */
    public function calcularInss(float $salario, ?string $dataReferencia = null): array
    {
        $dataReferencia = $dataReferencia ? "{$dataReferencia}-01" : null; // Converte para o primeiro dia do mês, se fornecido

        $tabela = TabelaInss::ativa()
                    ->vigente($dataReferencia)
                    ->with('faixas')
                    ->first();

        if (! $tabela) {
            throw new \RuntimeException('Nenhuma tabela INSS vigente encontrada.');
        }

        return $tabela->calcular($salario);
    }

    /**
     * Calcula IRRF com base na tabela vigente.
     */
    public function calcularIrrf(float $baseCalculo, int $dependentes = 0, float $pensao = 0, ?string $dataReferencia = null): array
    {
        $tabela = TabelaIrrf::ativa()
                    ->vigente($dataReferencia)
                    ->with('faixas')
                    ->first();

        if (! $tabela) {
            throw new \RuntimeException('Nenhuma tabela IRRF vigente encontrada.');
        }

        return $tabela->calcular($baseCalculo, $dependentes, $pensao);
    }

    /**
     * Atalho para cálculo completo (INSS + IRRF).
     */
    public function calcularFolha(float $salarioBruto, int $dependentes = 0, ?string $dataReferencia = null): array
    {
        $inss = $this->calcularInss($salarioBruto, $dataReferencia);

        // Base IRRF = Salário Bruto - INSS - Dedução Dependentes
        $tabelaIrrf = TabelaIrrf::ativa()->vigente($dataReferencia)->first();
        $deducaoDependentes = $dependentes * ($tabelaIrrf->deducao_dependente ?? 189.59);
        $baseIrrf = $salarioBruto - $inss['inss'] - $deducaoDependentes;

        $irrf = $baseIrrf > 0
            ? $this->calcularIrrf($baseIrrf, $dependentes, $dataReferencia)
            : ['irrf' => 0, 'aliquota_efetiva' => 0, 'detalhamento' => []];

        return [
            'salario_bruto' => $salarioBruto,
            'inss' => $inss,
            'irrf' => $irrf,
            'salario_liquido' => $salarioBruto - $inss['inss'] - $irrf['irrf'],
        ];
    }
}