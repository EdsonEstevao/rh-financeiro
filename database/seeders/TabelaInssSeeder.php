<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Domain\RH\{FaixaInss, TabelaInss};

class TabelaInssSeeder extends Seeder
{
    public function run(): void
    {
        // Evita duplicar caso execute o seeder mais de uma vez
        $existente = TabelaInss::where('ano_vigencia', 2026)
            ->where('ativo', true)
            ->first();

        if ($existente) {
            $this->command->info('Tabela INSS 2026 já existe. Pulando...');
            return;
        }

        $tabela = TabelaInss::create([
            'ano_vigencia' => 2026,
            'descricao' => 'Tabela INSS 2026 - Empregado, Doméstico e Avulso (Portaria MPS/MF nº 13/2026)',
            'ativo' => true,
            'vigencia_inicio' => '2026-01-01',
            'vigencia_fim' => null,
        ]);

        // Fonte: gov.br/inss - Portaria Interministerial MPS/MF Nº 13, de 09/01/2026
        // Teto de contribuição: R$ 8.475,55
        $faixas = [
            [
                'ordem' => 1,
                'teto' => 1621.00,
                'aliquota' => 0.0750,
                'deducao' => 0.00,
            ],
            [
                'ordem' => 2,
                'teto' => 2902.84,
                'aliquota' => 0.0900,
                'deducao' => 24.32,
            ],
            [
                'ordem' => 3,
                'teto' => 4354.27,
                'aliquota' => 0.1200,
                'deducao' => 62.77,
            ],
            [
                'ordem' => 4,
                'teto' => 8475.55,
                'aliquota' => 0.1400,
                'deducao' => 91.80,
            ],
        ];

        foreach ($faixas as $faixa) {
            FaixaInss::create([
                'tabela_inss_id' => $tabela->id,
                'ordem' => $faixa['ordem'],
                'teto' => $faixa['teto'],
                'aliquota' => $faixa['aliquota'],
                'deducao' => $faixa['deducao'],
            ]);
        }

        $this->command->info('Tabela INSS 2026 criada com sucesso!');
    }
}
