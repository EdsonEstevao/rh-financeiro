<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Domain\RH\{FaixaIrrf, TabelaIrrf};



class TabelaIrrfSeeder extends Seeder
{
    public function run(): void
    {
        $tabela = TabelaIrrf::create([
            'ano_vigencia' => 2025,
            'descricao' => 'Tabela IRRF 2025 (mensal)',
            'ativo' => true,
            'vigencia_inicio' => '2025-01-01',
            'vigencia_fim' => null,
            'deducao_dependente' => 189.59,
            'deducao_pensao' => 0,
        ]);

        // Tabela progressiva mensal IRRF 2025 (valores exemplos)
        $faixas = [
            ['ordem' => 1, 'teto' => 2259.20, 'aliquota' => 0.0000, 'deducao' => 0.00],
            ['ordem' => 2, 'teto' => 2826.65, 'aliquota' => 0.0750, 'deducao' => 169.44],
            ['ordem' => 3, 'teto' => 3751.05, 'aliquota' => 0.1500, 'deducao' => 381.44],
            ['ordem' => 4, 'teto' => 4664.68, 'aliquota' => 0.2250, 'deducao' => 662.77],
            ['ordem' => 5, 'teto' => 0.00,     'aliquota' => 0.2750, 'deducao' => 896.00], // teto 0 = sem limite
        ];

        foreach ($faixas as $faixa) {
            FaixaIrrf::create([
                'tabela_irrf_id' => $tabela->id,
                ...$faixa,
            ]);
        }

        $this->command->info('Tabela IRRF 2025 criada com sucesso!');
    }
}
