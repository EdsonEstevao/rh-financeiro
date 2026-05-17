<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Domain\RH\{FolhaPagamento, Funcionario};

class FolhaPagamentoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Gerando folhas de pagamento...');

        // ── Limpa a tabela respeitando FK ──────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        FolhaPagamento::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── Verifica se há funcionários ────────────────────────────
        $funcionarios = Funcionario::all();

        if ($funcionarios->isEmpty()) {
            $this->command->warn('⚠️  Nenhum funcionário encontrado. Execute FuncionarioSeeder antes.');
            return;
        }

        $competencias = [
            Carbon::now()->startOfMonth()->subMonths(2),
            Carbon::now()->startOfMonth()->subMonth(),
            Carbon::now()->startOfMonth(),
        ];

        $statusPorCompetencia = [
            0 => 'fechada',
            1 => 'fechada',
            2 => 'aberta',
        ];

        $totalCriadas = 0;

        foreach ($funcionarios as $funcionario) {
            foreach ($competencias as $index => $competencia) {

                $salarioBase     = (float) ($funcionario->salario ?? $this->salarioAleatorio());
                $inss            = $this->calcularInss($salarioBase);
                $quinto          = $this->calcularQuintoDiaUtil($competencia);
                $status          = $statusPorCompetencia[$index];

                $gratFeriado     = $this->sortear([0.00, 0.00, 150.00, 300.00]);
                $dsrHoraExtra    = $this->sortear([0.00, 0.00, 80.50, 120.75, 200.00]);
                $salFamiliaExtra = $this->sortear([0.00, 0.00, 0.00, 50.00]);
                $arredProvento   = $this->sortear([0.00, 0.01, 0.02, 0.03, 0.04, 0.05]);

                $valeDia20       = $this->sortear([0.00, 0.00, 220.00, 440.00]);
                $valeExtra       = $this->sortear([0.00, 0.00, 100.00, 200.00]);
                $faltasValor     = $this->sortear([0.00, 0.00, 0.00, 60.00, 120.00]);
                $dsrFaltas       = $faltasValor > 0
                                    ? round($faltasValor * 0.1667, 2)
                                    : 0.00;
                $arredDesconto   = $this->sortear([0.00, 0.01, 0.02, 0.03]);

                FolhaPagamento::create([
                    'funcionario_id'           => $funcionario->id,
                    'competencia'              => $competencia->format('Y-m-01'),
                    'salario_base'             => $salarioBase,
                    'horas_extras_totais'      => $this->sortear([0, 0, 10, 15, 20, 25]),  // NOVO
                    'valor_hora_extra'         => round($salarioBase / 220 * 1.5, 2),       // NOVO
                    'gratificacao_feriado'     => $gratFeriado,
                    // 'dsr_hora_extra'        => REMOVIDO (agora é calculado automaticamente)
                    'salario_familia_hr_extra' => $salFamiliaExtra,
                    'arredondamento_provento'  => $arredProvento,
                    'desconto_inss'            => $inss,
                    'vale_dia_20'              => $valeDia20,
                    'vale_extra'               => $valeExtra,
                    'faltas_valor'             => $faltasValor,
                    'dsr_faltas'               => $dsrFaltas,
                    'arredondamento_desconto'  => $arredDesconto,
                    'quinto_dia_util'          => $quinto,
                    'observacao'               => $this->observacaoAleatoria($status, $faltasValor),
                    'status'                   => $status,
                ]);

                $totalCriadas++;

                $cor = $status === 'aberta' ? 'green' : 'yellow';
                $liquido = $salarioBase + $gratFeriado + $dsrHoraExtra - $inss - $valeDia20 - $faltasValor;

                $this->command->line(
                    "  ✅ <fg=cyan>{$funcionario->nome}</> | " .
                    "<fg=yellow>{$competencia->format('m/Y')}</> | " .
                    "Status: <fg={$cor}>{$status}</> | " .
                    "Líquido: <fg=white>R$ " . number_format($liquido, 2, ',', '.') . "</>"
                );
            }
        }

        $this->command->newLine();
        $this->command->info("🎉 Total de folhas criadas: {$totalCriadas}");
    }


    // ─── HELPERS ───────────────────────────────────────────────────

    private function calcularInss(float $salario): float
    {
        return match (true) {
            $salario <= 1412.00 => round($salario * 0.075, 2),
            $salario <= 2666.68 => round($salario * 0.09,  2),
            $salario <= 4000.03 => round($salario * 0.12,  2),
            $salario <= 7786.02 => round($salario * 0.14,  2),
            default             => 908.86,
        };
    }

    /**
     * Sorteia um valor NUMÉRICO (float)
     */
    private function sortear(array $opcoes): float
    {
        return (float) $opcoes[array_rand($opcoes)];
    }

    /**
     * Sorteia um valor TEXTUAL ou NULL
     */
    private function sortearTexto(array $opcoes): ?string
    {
        return $opcoes[array_rand($opcoes)];
    }

    private function salarioAleatorio(): float
    {
        $faixas = [
            1412.00,
            1800.00,
            2200.00,
            2800.00,
            3500.00,
            4500.00,
            6000.00,
        ];

        return $faixas[array_rand($faixas)];
    }

    private function calcularQuintoDiaUtil(Carbon $competencia): string
    {
        $data  = $competencia->copy()->startOfMonth();
        $uteis = 0;

        while ($uteis < 5) {
            if ($data->isWeekday()) {
                $uteis++;
            }
            if ($uteis < 5) {
                $data->addDay();
            }
        }

        return $data->format('Y-m-d');
    }

    private function observacaoAleatoria(string $status, float $faltasValor = 0.0): ?string
    {
        if ($status === 'aberta') {
            return $this->sortearTexto([
                null,
                null,
                'Aguardando validação do gestor.',
                'Folha em processamento.',
            ]);
        }

        if ($faltasValor > 0) {
            return $this->sortearTexto([
                'Desconto de falta aplicado conforme registro de ponto.',
                'Falta justificada — desconto mantido conforme política interna.',
                'Ausência não justificada no período.',
            ]);
        }

        return $this->sortearTexto([
            null,
            null,
            null,
            'Folha fechada sem pendências.',
            'Competência processada e validada pelo RH.',
        ]);
    }
}
